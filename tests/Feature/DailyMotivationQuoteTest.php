<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use App\Models\User;
use App\Models\Gym;
use App\Livewire\Member\DailyMotivationQuote;

class DailyMotivationQuoteTest extends TestCase
{
    use RefreshDatabase;

    protected $gym;
    protected $member;

    protected $curatedTexts = [
        'Consistency is the secret to unlocking your strength, one workout at a time.',
        'Discipline is choosing between what you want now and what you want most.',
        'Nourish your body, strengthen your mind, and focus on the small victories today.',
        'Your health is an investment, not an expense. Show up for yourself today.',
        'Wellness is a daily commitment to small, positive habits that build a stronger you.',
        'Success is the sum of small fitness habits repeated day in and day out.',
        'Discipline builds consistency, and consistency transforms your life.',
        'Productivity starts with a clear mind and an energized body. Breathe, focus, and move.',
        'Your body can stand almost anything. It\'s your mind that you have to convince.',
        'Consistency over perfection. Every single step counts towards your wellness journey.'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache before each test
        Cache::forget('zenquotes_random_quote');

        // Create mock gym and member user
        $this->gym = Gym::create([
            'name' => 'Quote Gym Central',
            'email' => 'quote.central@gym.com',
            'subscription_status' => 'active',
        ]);

        $this->member = User::create([
            'name' => 'Zen Member',
            'email' => 'zen.member@gym.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'gym_id' => $this->gym->id,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test the motivation quote component renders quote successfully from API when relevant.
     */
    public function test_component_fetches_and_renders_quote_successfully(): void
    {
        Http::fake([
            'zenquotes.io/api/random' => Http::response([
                [
                    'q' => 'Your fitness is your own responsibility.',
                    'a' => 'Unknown Author'
                ]
            ], 200)
        ]);

        Livewire::test(DailyMotivationQuote::class)
            ->assertSee('Your fitness is your own responsibility.')
            ->assertSee('— Unknown Author');
    }

    /**
     * Test that irrelevant quotes from the API are rejected in favor of curated quotes.
     */
    public function test_irrelevant_api_quotes_are_rejected_in_favor_of_curated_quotes(): void
    {
        Http::fake([
            'zenquotes.io/api/random' => Http::response([
                [
                    'q' => 'The quick brown fox jumps over the lazy dog.',
                    'a' => 'Famous Author'
                ]
            ], 200)
        ]);

        $component = Livewire::test(DailyMotivationQuote::class);
        
        // The irrelevant quote should not be displayed
        $component->assertDontSee('The quick brown fox jumps over the lazy dog.');
        $component->assertDontSee('— Famous Author');
        
        // Instead, a curated quote should be displayed
        $text = $component->get('quoteText');
        $this->assertNotEquals('The quick brown fox jumps over the lazy dog.', $text);
        $this->assertContains($text, $this->curatedTexts);
    }

    /**
     * Test the component uses cached quote on subsequent loads.
     */
    public function test_component_uses_cached_quote_without_extra_api_calls(): void
    {
        // First request fetches from API and caches it
        Http::fake([
            'zenquotes.io/api/random' => Http::response([
                [
                    'q' => 'First API fitness Quote',
                    'a' => 'Author One'
                ]
            ], 200)
        ]);

        Livewire::test(DailyMotivationQuote::class)
            ->assertSee('First API fitness Quote');

        // Stub the API with a different quote, but it should NOT be called because it is cached
        Http::fake([
            'zenquotes.io/api/random' => Http::response([
                [
                    'q' => 'Second API fitness Quote (Should not be fetched)',
                    'a' => 'Author Two'
                ]
            ], 200)
        ]);

        Livewire::test(DailyMotivationQuote::class)
            ->assertSee('First API fitness Quote')
            ->assertDontSee('Second API fitness Quote');
    }

    /**
     * Test the refresh button bypasses cache to get a fresh quote.
     */
    public function test_refresh_button_bypasses_cache_to_get_fresh_quote(): void
    {
        Http::fakeSequence()
            ->push([['q' => 'Cached fitness Quote Text', 'a' => 'Cached Author']], 200)
            ->push([['q' => 'Fresh fitness Quote Text', 'a' => 'Fresh Author']], 200);

        Livewire::test(DailyMotivationQuote::class)
            ->assertSee('Cached fitness Quote Text')
            // Click the refresh button
            ->call('refreshQuote')
            ->assertSee('Fresh fitness Quote Text')
            ->assertDontSee('Cached fitness Quote Text');
    }

    /**
     * Test the component falls back gracefully on API failure.
     */
    public function test_component_falls_back_gracefully_on_api_failure(): void
    {
        // Mock API error
        Http::fake([
            'zenquotes.io/api/random' => Http::response('Error', 500)
        ]);

        $component = Livewire::test(DailyMotivationQuote::class);
        $text = $component->get('quoteText');
        $author = $component->get('quoteAuthor');

        $this->assertContains($text, $this->curatedTexts);
        $this->assertContains($author, ['GlowGym Wellness', 'GlowGym Motivation']);
    }

    /**
     * Test that member dashboard integrates and renders the quote component.
     */
    public function test_member_dashboard_renders_motivation_component(): void
    {
        Http::fake([
            'zenquotes.io/api/random' => Http::response([
                [
                    'q' => 'Dashboard integration fitness quote.',
                    'a' => 'Fitness Coach'
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->member)->get('/member/dashboard');

        $response->assertStatus(200)
            ->assertSeeLivewire(DailyMotivationQuote::class)
            ->assertSee('Dashboard integration fitness quote.')
            ->assertSee('Fitness Coach');
    }
}
