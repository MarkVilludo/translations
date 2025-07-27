<?php

namespace App\Actions\Translations;

use App\Http\Requests\UpdateTranslationRequest;
use App\Http\Resources\Translation\TranslationResource;
use App\Models\Locale;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateTranslation
{
    use AsAction;

    public function handle(string $key, array $data): array
    {
        $translations = [];
    
        $translationKey = TranslationKey::where('key', $key)->firstOrFail();
        $providedLocaleCodes = array_keys($data['translations']);
        $existingTranslations = Translation::where('translation_key_id', $translationKey->id)->get();
    
        // Delete translations not present in the current request
        foreach ($existingTranslations as $existingTranslation) {
            $localeCode = $existingTranslation->locale->code;
    
            if (!in_array($localeCode, $providedLocaleCodes)) {
                $existingTranslation->delete();
            }
        }
    
        // Create/update translations provided in the request
        foreach ($data['translations'] as $localeCode => $content) {
            $locale = Locale::where('code', $localeCode)->firstOrFail();
    
            $translation = Translation::updateOrCreate(
                ['translation_key_id' => $translationKey->id, 'locale_id' => $locale->id],
                ['content' => $content]
            );
    
            if (isset($data['tags'])) {
                $translation->tags()->sync($data['tags']);
            }
    
            $translations[] = $translation;
        }
    
        return $translations;
    }

    public function asController(UpdateTranslationRequest $request, string $key): JsonResponse
    {
        $validated = $request->validated();

        $translations = $this->handle($key, $validated);

        return response()->json([
            'data' => TranslationResource::collection($translations),
        ]);
    }
}
