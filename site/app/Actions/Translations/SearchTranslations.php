<?php

namespace App\Actions\Translations;

use App\Models\Translation;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Action;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Translation\TranslationResource;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsController;

class SearchTranslations
{
    use AsController;

    public function handle(Request $request)
    {
        $query = Translation::query()->with(['tags', 'locale', 'translationKey']);

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->tag . '%');
            });
        }

        if ($request->filled('key')) {
            $query->whereHas('translationKey', function ($q) use ($request) {
                $q->where('key', 'like', '%' . $request->key . '%');
            });
        }

        if ($request->filled('content')) {
            $query->where('content', 'like', '%' . $request->content . '%');
        }

        return $query
            ->orderBy('id')
            ->paginate($request->get('per_page', 10));
    }

    public function asController(Request $request): JsonResponse
    {
        $translations = $this->handle($request);

        return response()->json([
            'data' => TranslationResource::collection($translations),
            'meta' => [
                'current_page' => $translations->currentPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
            ]
        ]);
    }
}
