<?php

namespace App\Actions\Translations;

use App\Models\TranslationKey;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsController;

class GetTranslation
{
    use AsController;

    public function handle(string $key): TranslationKey
    {
        return TranslationKey::with(['translations.locale', 'translations.tags'])
            ->where('key', $key)
            ->firstOrFail();
    }

    public function asController(string $key): JsonResponse
    {
        $translationKey = $this->handle($key);

        $translations = $translationKey->translations->mapWithKeys(function ($translation) {
            return [$translation->locale->code => $translation->content];
        });

        // Assuming tags are shared and you can get them from any translation (or from the key if normalized)
        $tags = $translationKey->translations->first()?->tags->pluck('id')->toArray() ?? [];

        return response()->json([
            'key' => $translationKey->key,
            'translations' => $translations,
            'tags' => $tags,
        ]);
    }
}
