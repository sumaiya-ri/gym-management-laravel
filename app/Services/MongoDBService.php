<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MongoDBService
{
    /**
     * Cache the availability status of MongoDB.
     */
    protected static $mongoAvailable = null;

    /**
     * Check if a valid MongoDB connection exists and is reachable.
     */
    public static function isMongoAvailable(): bool
    {
        if (self::$mongoAvailable !== null) {
            return self::$mongoAvailable;
        }

        try {
            $connection = DB::connection('mongodb');
            // Execute a simple ping command to verify connection and auth status
            $connection->getMongoDB()->command(['ping' => 1]);
            self::$mongoAvailable = true;
        } catch (\Exception $e) {
            Log::warning("MongoDB Atlas connection failed. Falling back to MySQL mock. Error: " . $e->getMessage());
            self::$mongoAvailable = false;
        }

        return self::$mongoAvailable;
    }

    /**
     * Get a collection instance.
     */
    public static function collection(string $name): MongoDBCollection
    {
        return new MongoDBCollection($name);
    }
}

class MongoDBCollection
{
    protected $collectionName;
    protected $delegate;

    public function __construct(string $collectionName)
    {
        $this->collectionName = $collectionName;
        if (MongoDBService::isMongoAvailable()) {
            $this->delegate = new RealMongoCollection($collectionName);
        } else {
            $this->delegate = new MongoCollectionMock($collectionName);
        }
    }

    public function insertOne(array $document): bool
    {
        return $this->delegate->insertOne($document);
    }

    public function insertMany(array $documents): bool
    {
        return $this->delegate->insertMany($documents);
    }

    public function find(array $filter = []): array
    {
        return $this->delegate->find($filter);
    }

    public function aggregate(array $pipeline): array
    {
        return $this->delegate->aggregate($pipeline);
    }

    public function deleteMany(array $filter = []): bool
    {
        return $this->delegate->deleteMany($filter);
    }
}

class RealMongoCollection
{
    protected $collectionName;
    protected $collection;

    public function __construct(string $collectionName)
    {
        $this->collectionName = $collectionName;
        $this->collection = DB::connection('mongodb')->getCollection($collectionName);
    }

    public function insertOne(array $document): bool
    {
        if (!isset($document['created_at'])) {
            $document['created_at'] = new \MongoDB\BSON\UTCDateTime(now());
        } elseif (is_string($document['created_at'])) {
            $document['created_at'] = new \MongoDB\BSON\UTCDateTime(strtotime($document['created_at']) * 1000);
        }

        $this->collection->insertOne($document);
        return true;
    }

    public function insertMany(array $documents): bool
    {
        foreach ($documents as &$doc) {
            if (!isset($doc['created_at'])) {
                $doc['created_at'] = new \MongoDB\BSON\UTCDateTime(now());
            } elseif (is_string($doc['created_at'])) {
                $doc['created_at'] = new \MongoDB\BSON\UTCDateTime(strtotime($doc['created_at']) * 1000);
            }
        }
        $this->collection->insertMany($documents);
        return true;
    }

    public function find(array $filter = []): array
    {
        $cursor = $this->collection->find($filter);
        $results = [];
        foreach ($cursor as $doc) {
            $array = json_decode(json_encode($doc), true);
            
            if (isset($doc['_id']) && $doc['_id'] instanceof \MongoDB\BSON\ObjectId) {
                $array['_id'] = (string)$doc['_id'];
            }
            
            if (isset($doc['created_at']) && $doc['created_at'] instanceof \MongoDB\BSON\UTCDateTime) {
                $array['created_at'] = $doc['created_at']->toDateTime()->setTimezone(new \DateTimeZone(config('app.timezone', 'UTC')))->format('Y-m-d H:i:s');
            }
            
            $results[] = $array;
        }
        return $results;
    }

    public function aggregate(array $pipeline): array
    {
        $pipeline = $this->transformPipeline($pipeline);
        $cursor = $this->collection->aggregate($pipeline);
        $results = [];
        foreach ($cursor as $doc) {
            $array = json_decode(json_encode($doc), true);

            if (isset($doc['_id']) && $doc['_id'] instanceof \MongoDB\BSON\ObjectId) {
                $array['_id'] = (string)$doc['_id'];
            }

            if (isset($doc['created_at']) && $doc['created_at'] instanceof \MongoDB\BSON\UTCDateTime) {
                $array['created_at'] = $doc['created_at']->toDateTime()->setTimezone(new \DateTimeZone(config('app.timezone', 'UTC')))->format('Y-m-d H:i:s');
            }

            $results[] = $array;
        }
        return $results;
    }

    public function deleteMany(array $filter = []): bool
    {
        $this->collection->deleteMany($filter);
        return true;
    }

    protected function transformPipeline(array $pipeline): array
    {
        return array_map(function ($stage) {
            if (isset($stage['$group']['_id']) && is_array($stage['$group']['_id'])) {
                $idExpr = $stage['$group']['_id'];
                if (isset($idExpr['$month']) && $idExpr['$month'] === '$created_at') {
                    $stage['$group']['_id'] = [
                        '$dateToString' => [
                            'format' => '%Y-%m',
                            'date' => '$created_at'
                        ]
                    ];
                }
            }
            return $stage;
        }, $pipeline);
    }
}

class MongoCollectionMock
{
    protected $collectionName;

    public function __construct(string $collectionName)
    {
        $this->collectionName = $collectionName;
    }

    public function insertOne(array $document): bool
    {
        if (!isset($document['created_at'])) {
            $document['created_at'] = now()->toDateTimeString();
        }

        DB::table('mongodb_collections')->insert([
            'collection' => $this->collectionName,
            'document' => json_encode($document),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    public function insertMany(array $documents): bool
    {
        foreach ($documents as $doc) {
            $this->insertOne($doc);
        }
        return true;
    }

    public function find(array $filter = []): array
    {
        $rows = DB::table('mongodb_collections')
            ->where('collection', $this->collectionName)
            ->get();

        $results = [];
        foreach ($rows as $row) {
            $doc = json_decode($row->document, true);
            if ($this->matchesFilter($doc, $filter)) {
                $results[] = $doc;
            }
        }

        return $results;
    }

    public function aggregate(array $pipeline): array
    {
        $rows = DB::table('mongodb_collections')
            ->where('collection', $this->collectionName)
            ->get();

        $data = [];
        foreach ($rows as $row) {
            $data[] = json_decode($row->document, true);
        }

        foreach ($pipeline as $stage) {
            foreach ($stage as $stageName => $stageParams) {
                switch ($stageName) {
                    case '$match':
                        $data = $this->processMatch($data, $stageParams);
                        break;
                    case '$group':
                        $data = $this->processGroup($data, $stageParams);
                        break;
                    case '$sort':
                        $data = $this->processSort($data, $stageParams);
                        break;
                    case '$limit':
                        $data = array_slice($data, 0, $stageParams);
                        break;
                }
            }
        }

        return $data;
    }

    public function deleteMany(array $filter = []): bool
    {
        if (empty($filter)) {
            DB::table('mongodb_collections')->where('collection', $this->collectionName)->delete();
            return true;
        }

        $rows = DB::table('mongodb_collections')
            ->where('collection', $this->collectionName)
            ->get();

        foreach ($rows as $row) {
            $doc = json_decode($row->document, true);
            if ($this->matchesFilter($doc, $filter)) {
                DB::table('mongodb_collections')->where('id', $row->id)->delete();
            }
        }

        return true;
    }

    protected function processMatch(array $data, array $filter): array
    {
        $filtered = [];
        foreach ($data as $doc) {
            if ($this->matchesFilter($doc, $filter)) {
                $filtered[] = $doc;
            }
        }
        return $filtered;
    }

    protected function processGroup(array $data, array $groupParams): array
    {
        $idExpr = $groupParams['_id'] ?? null;
        $groups = [];

        foreach ($data as $doc) {
            $groupKey = 'null';
            if (is_string($idExpr) && str_starts_with($idExpr, '$')) {
                $field = substr($idExpr, 1);
                $groupKey = $doc[$field] ?? 'null';
            } elseif (is_array($idExpr)) {
                if (isset($idExpr['$month'])) {
                    $dateField = substr($idExpr['$month'], 1);
                    $dateVal = $doc[$dateField] ?? null;
                    $groupKey = $dateVal ? date('Y-m', strtotime($dateVal)) : 'null';
                }
            }

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [];
            }
            $groups[$groupKey][] = $doc;
        }

        $results = [];
        foreach ($groups as $key => $docs) {
            $groupedDoc = ['_id' => $key];

            foreach ($groupParams as $field => $expr) {
                if ($field === '_id') {
                    continue;
                }

                foreach ($expr as $op => $opVal) {
                    switch ($op) {
                        case '$sum':
                            $sum = 0;
                            if ($opVal === 1) {
                                $sum = count($docs);
                            } elseif (is_string($opVal) && str_starts_with($opVal, '$')) {
                                $sumField = substr($opVal, 1);
                                foreach ($docs as $d) {
                                    $sum += (float)($d[$sumField] ?? 0);
                                }
                            }
                            $groupedDoc[$field] = $sum;
                            break;

                        case '$count':
                            $groupedDoc[$field] = count($docs);
                            break;

                        case '$avg':
                            $avgField = substr($opVal, 1);
                            $sum = 0;
                            foreach ($docs as $d) {
                                $sum += (float)($d[$avgField] ?? 0);
                            }
                            $groupedDoc[$field] = count($docs) > 0 ? ($sum / count($docs)) : 0;
                            break;
                    }
                }
            }

            $results[] = $groupedDoc;
        }

        return $results;
    }

    protected function processSort(array $data, array $sortParams): array
    {
        usort($data, function ($a, $b) use ($sortParams) {
            foreach ($sortParams as $field => $dir) {
                $valA = $a[$field] ?? null;
                $valB = $b[$field] ?? null;

                if ($valA == $valB) {
                    continue;
                }

                if ($dir === -1) {
                    return $valA < $valB ? 1 : -1;
                } else {
                    return $valA > $valB ? 1 : -1;
                }
            }
            return 0;
        });

        return $data;
    }

    protected function matchesFilter(array $doc, array $filter): bool
    {
        foreach ($filter as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $op => $opVal) {
                    $docVal = $doc[$key] ?? null;
                    switch ($op) {
                        case '$gte':
                            if ($docVal < $opVal) return false;
                            break;
                        case '$lte':
                            if ($docVal > $opVal) return false;
                            break;
                        case '$gt':
                            if ($docVal <= $opVal) return false;
                            break;
                        case '$lt':
                            if ($docVal >= $opVal) return false;
                            break;
                        case '$ne':
                            if ($docVal == $opVal) return false;
                            break;
                    }
                }
            } else {
                if (($doc[$key] ?? null) != $val) {
                    return false;
                }
            }
        }
        return true;
    }
}
