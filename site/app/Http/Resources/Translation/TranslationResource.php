<?php

namespace App\Http\Resources\Translation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'locale' => $this->locale->code ?? null,
            'key' => $this->translationKey->key ?? null,
            'tags' => $this->tags->pluck('name'),
            'content' => $this->content
        ];
    }
}
