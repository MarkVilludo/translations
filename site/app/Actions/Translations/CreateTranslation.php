<?php

namespace App\Actions\Translations;

use App\Http\Requests\CreateTranslationRequest;
use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\Locale;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Translation\TranslationResource;
use Lorisleiva\Actions\Concerns\AsController;

class CreateTranslation
{
    use AsController;

    /**
     * Handle creating multiple translations for different locales.
     *
     * @param array $data
     * @return Collection
     */
    public function handle(array $data): Collection
    {
        $createdTranslations = collect();
    
        // Get or create the translation key
        $translationKey = TranslationKey::firstOrCreate(['key' => $data['key']]);
    
        foreach ($data['translations'] as $localeCode => $content) {
            $locale = Locale::where('code', $localeCode)->firstOrFail();
    
            $translation = Translation::updateOrCreate(
                ['translation_key_id' => $translationKey->id, 'locale_id' => $locale->id],
                ['content' => $content]
            );
    
            if (!empty($data['tags'])) {
                $translation->tags()->sync($data['tags']);
            }
    
            $createdTranslations->push($translation);
        }
    
        return $createdTranslations;
    }

    /**
     * Handle the incoming controller request.
     *
     * @param CreateTranslationRequest $request
     * @return JsonResponse
     */
    public function asController(CreateTranslationRequest $request): JsonResponse
    {
        $translations = $this->handle($request->validated());

        return response()->json([
            'data' => TranslationResource::collection($translations)
        ], 201);
    }
}
