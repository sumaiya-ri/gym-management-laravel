<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MongoDBService
{
    /**
     * Get a mock collection instance.
     */
    public static function collection(string $name): MongoCollectionMock
    {
        return new MongoCollectionMock($name);
    }
}

class MongoCollectionMock
{
    protected $collectionName;

    public function __construct(string $collectionName)
    {
        $this->collectionName = $collectionName;
    }

    /**
     * Insert a single document into the mock collection.
     */
    public function insertOne(array $document): bool
    {
        // Add timestamps automatically if not set
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

    /**
     * Insert multiple documents.
     */
    public function insertMany(array $documents): bool
    {
        foreach ($documents as $doc) {
            $this->insertOne($doc);
        }
        return true;
    }

    /**
     * Retrieve documents matching a filter.
     */
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

    /**
     * Aggregate pipeline helper.
     */
    public function aggregate(array $pipeline): array
    {
        // Get all documents in the collection
        $rows = DB::table('mongodb_collections')
            ->where('collection', $this->collectionName)
            ->get();

        $data = [];
        foreach ($rows as $row) {
            $data[] = json_decode($row->document, true);
        }

        // Process stages sequentially
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

    /**
     * Match stage processing.
     */
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

    /**
     * Group stage processing.
     */
    protected function processGroup(array $data, array $groupParams): array
    {
        $idExpr = $groupParams['_id'] ?? null;
        $groups = [];

        foreach ($data as $doc) {
            // Resolve group key value (e.g. '$gym_name' => value of $doc['gym_name'])
            $groupKey = 'null';
            if (is_string($idExpr) && str_starts_with($idExpr, '$')) {
                $field = substr($idExpr, 1);
                $groupKey = $doc[$field] ?? 'null';
            } elseif (is_array($idExpr)) {
                // Support complex expressions (like date formats)
                // For simplicity of monthly revenue, check if expression resolves month
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

    /**
     * Sort stage processing.
     */
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

    /**
     * Helper to verify if document matches standard filter.
     */
    protected function matchesFilter(array $doc, array $filter): bool
    {
        foreach ($filter as $key => $val) {
            if (is_array($val)) {
                // Support operator queries (like $gte, $lte)
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
