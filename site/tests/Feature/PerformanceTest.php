<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;
use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\Translation;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $locale = Locale::firstOrCreate(
            ['code' => 'en'],
            ['name' => 'English']
        );

        $translationKey = TranslationKey::firstOrCreate(
            ['key' => 'test.greeting'],
            ['name' => 'Test Greeting Key']
        );

        Translation::create([
            'locale_id' => $locale->id,
            'translation_key_id' => $translationKey->id,
            'content' => 'Hello from test!',
        ]);

        User::factory()->create();
    }

    public function test_json_export_endpoint_response_time_is_under_500ms(): void
    {
        $user = User::first();
        $token = $user->createToken('performance-test-token')->plainTextToken;

        $locale = 'en';

        $startTime = microtime(true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson("/api/translations/exports?locale={$locale}");

        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $response->assertJson([
            'test.greeting' => 'Hello from test!',
        ]);
        $this->assertCount(1, $response->json());

        $this->assertLessThan(
            500,
            $duration,
            "The JSON export endpoint for locale '{$locale}' took {$duration}ms, which exceeds the 500ms limit."
        );
    }
}
