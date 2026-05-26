<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DailyMotivationQuote extends Component
{
    /**
     * The motivational quote text.
     */
    public $quoteText = '';

    /**
     * The quote author.
     */
    public $quoteAuthor = '';

    /**
     * Whether the current quote is a fallback/default quote due to API failure.
     */
    public $isFallback = false;

    /**
     * A curated list of fitness, wellness, consistency, and discipline quotes.
     * Aligned with GlowGym women's wellness branding.
     */
    protected $curatedQuotes = [
        [
            'quote' => 'Consistency is the secret to unlocking your strength, one workout at a time.',
            'author' => 'GlowGym Wellness'
        ],
        [
            'quote' => 'Discipline is choosing between what you want now and what you want most.',
            'author' => 'GlowGym Motivation'
        ],
        [
            'quote' => 'Nourish your body, strengthen your mind, and focus on the small victories today.',
            'author' => 'GlowGym Wellness'
        ],
        [
            'quote' => 'Your health is an investment, not an expense. Show up for yourself today.',
            'author' => 'GlowGym Wellness'
        ],
        [
            'quote' => 'Wellness is a daily commitment to small, positive habits that build a stronger you.',
            'author' => 'GlowGym Wellness'
        ],
        [
            'quote' => 'Success is the sum of small fitness habits repeated day in and day out.',
            'author' => 'GlowGym Motivation'
        ],
        [
            'quote' => 'Discipline builds consistency, and consistency transforms your life.',
            'author' => 'GlowGym Wellness'
        ],
        [
            'quote' => 'Productivity starts with a clear mind and an energized body. Breathe, focus, and move.',
            'author' => 'GlowGym Wellness'
        ],
        [
            'quote' => 'Your body can stand almost anything. It\'s your mind that you have to convince.',
            'author' => 'GlowGym Motivation'
        ],
        [
            'quote' => 'Consistency over perfection. Every single step counts towards your wellness journey.',
            'author' => 'GlowGym Wellness'
        ]
    ];

    /**
     * Keywords to detect if a quote is fitness/wellness/discipline/productivity related.
     */
    protected $keywords = [
        'health', 'fitness', 'discipline', 'productivity', 'wellness', 'consistency', 
        'workout', 'exercise', 'gym', 'strength', 'habit', 'routine', 'energy', 'focus', 
        'goals', 'strive', 'achieve', 'train', 'healthy', 'nourish', 'action', 'move', 
        'body', 'mind', 'power', 'muscle', 'diet', 'nutrition', 'lifestyle', 'sweat', 
        'physical', 'sleep', 'rest'
    ];

    /**
     * Mount the component.
     */
    public function mount()
    {
        $this->loadQuote(false);
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.member.daily-motivation-quote');
    }

    /**
     * Refresh and fetch a new quote, bypassing the cache.
     */
    public function refreshQuote()
    {
        $this->loadQuote(true);
    }

    /**
     * Verify if the quote is related to fitness, wellness, discipline, or productivity.
     */
    protected function isQuoteRelevant(string $text): bool
    {
        $textLower = strtolower($text);
        foreach ($this->keywords as $keyword) {
            if (str_contains($textLower, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Load the quote from cache or external API.
     */
    protected function loadQuote(bool $forceRefresh)
    {
        $cacheKey = 'zenquotes_random_quote';

        if (!$forceRefresh && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            $this->quoteText = $cached['quote'];
            $this->quoteAuthor = $cached['author'];
            $this->isFallback = false;
            return;
        }

        try {
            // Set connection and request timeouts to handle API unavailability gracefully
            $response = Http::timeout(5)->connectTimeout(3)->get('https://zenquotes.io/api/random');

            if ($response->successful()) {
                $data = $response->json();
                
                if (is_array($data) && isset($data[0]['q']) && isset($data[0]['a'])) {
                    $q = $data[0]['q'];
                    $a = $data[0]['a'];

                    // Check if quote is relevant to wellness and fitness
                    if ($this->isQuoteRelevant($q)) {
                        $this->quoteText = $q;
                        $this->quoteAuthor = $a;
                        $this->isFallback = false;

                        // Cache the quote for 15 minutes
                        Cache::put($cacheKey, ['quote' => $q, 'author' => $a], now()->addMinutes(15));

                        Log::info('Successfully fetched relevant quote from ZenQuotes API.', [
                            'quote' => $q,
                            'author' => $a
                        ]);

                        return;
                    } else {
                        Log::info('Fetched ZenQuotes quote was too generic. Using a curated wellness quote instead.', [
                            'quote' => $q,
                            'author' => $a
                        ]);

                        // Pick a random curated quote, update the cache with it, and return!
                        $randomCurated = $this->curatedQuotes[array_rand($this->curatedQuotes)];
                        $this->quoteText = $randomCurated['quote'];
                        $this->quoteAuthor = $randomCurated['author'];
                        $this->isFallback = false;

                        Cache::put($cacheKey, ['quote' => $this->quoteText, 'author' => $this->quoteAuthor], now()->addMinutes(15));
                        return;
                    }
                }
            } else {
                Log::warning('ZenQuotes API returned unsuccessful response or invalid format.', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('ZenQuotes API request timed out or connection failed.', [
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch quote from ZenQuotes API due to unexpected error.', [
                'error' => $e->getMessage()
            ]);
        }

        // Curated Quote Fallback Path:
        // If the API request failed or returned an irrelevant quote:
        // Try to keep using cached quote if it exists (even during force refresh if API fails)
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            $this->quoteText = $cached['quote'];
            $this->quoteAuthor = $cached['author'];
            $this->isFallback = false;
        } else {
            // Pick a random quote from our curated fallback list
            $randomCurated = $this->curatedQuotes[array_rand($this->curatedQuotes)];
            $this->quoteText = $randomCurated['quote'];
            $this->quoteAuthor = $randomCurated['author'];
            $this->isFallback = true;
        }
    }
}
