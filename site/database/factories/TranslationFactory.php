<?php

namespace Database\Factories;

use App\Models\Translation;
use App\Models\Locale;
use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        // Create new unique locale and translation key models
        $locale = Locale::factory()->create();
        $translationKey = TranslationKey::factory()->create();

        return [
            'locale_id' => $locale->id,
            'translation_key_id' => $translationKey->id,
            'content' => $this->faker->sentence,
        ];
    }
}
