<?php

namespace App\Http\Resources\Translation;

use Illuminate\Http\Resources\Json\JsonResource;

class TranslationKeyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'key' => $this->key,
            'translations' => $this->translations->mapWithKeys(function ($translation) {
                return [$translation->locale->code => $translation->content];
            }),
            'tags' => $this->translations->first()?->tags->pluck('id') ?? [],
        ];
    }
}
