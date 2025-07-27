<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\Translation;
use Illuminate\Support\Facades\DB; // Import DB facade for batch inserts

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfUniqueKeys = 50000; // 50,000 unique keys * 2 locales = 100,000 translation records
        $chunkSize = 2000; // Adjust chunk size based on your server's memory limits

        $enLocale = Locale::firstOrCreate(['code' => 'en'], ['name' => 'English']);
        $frLocale = Locale::firstOrCreate(['code' => 'fr'], ['name' => 'French']);

        $localeIds = [
            'en' => $enLocale->id,
            'fr' => $frLocale->id,
        ];

        $translationKeysData = [];
        for ($i = 0; $i < $numberOfUniqueKeys; $i++) {
            $translationKeysData[] = [
                'key' => 'app.message_' . ($i + 1),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($translationKeysData) >= $chunkSize) {
                DB::table('translation_keys')->insert($translationKeysData);
                $translationKeysData = [];
            }
        }
        if (!empty($translationKeysData)) {
            DB::table('translation_keys')->insert($translationKeysData);
        }

        $allTranslationKeys = TranslationKey::all()->pluck('id', 'key')->toArray();

        $translationsToInsert = [];
        $keyCounter = 0;

        foreach ($allTranslationKeys as $key => $translationKeyId) {
            $keyCounter++;

            $translationsToInsert[] = [
                'locale_id' => $localeIds['en'],
                'translation_key_id' => $translationKeyId,
                'content' => "This is the English content for key {$keyCounter}.",
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $translationsToInsert[] = [
                'locale_id' => $localeIds['fr'],
                'translation_key_id' => $translationKeyId,
                'content' => "Ceci est le French content for key {$keyCounter}.",
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($translationsToInsert) >= $chunkSize * count($localeIds)) {
                DB::table('translations')->insert($translationsToInsert);
                $translationsToInsert = [];
            }
        }
        if (!empty($translationsToInsert)) {
            DB::table('translations')->insert($translationsToInsert);
        }

        if ($this->command) {
            $this->command->info('Translation seeding completed.');
        }
    }
}
