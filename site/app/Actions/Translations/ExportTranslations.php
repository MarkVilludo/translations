<?php

namespace App\Actions\Translations;

use App\Models\Translation; // Still needed if you use Eloquent models elsewhere, but not directly for the optimized query
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Essential for direct database queries
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Action;
use Illuminate\Http\JsonResponse;

class ExportTranslations extends Action
{
    use AsController;

    /**
     * Handle the action to export translations for a given locale.
     * This method is optimized to efficiently retrieve a large number of translations
     * by performing direct database joins and selecting only necessary columns.
     *
     * @param string $locale The locale code (e.g., 'en', 'fr').
     * @return array An associative array of translations (key => content).
     */
    public function handle(string $locale): array
    {
        // Step 1: Efficiently find the ID of the given locale.
        // Using DB::table()->value() is faster than fetching a full Eloquent model
        // when you only need a single column's value.
        $localeId = DB::table('locales')->where('code', $locale)->value('id');

        // If the locale does not exist, return an empty array immediately.
        if (!$localeId) {
            return [];
        }

        // Step 2: Perform a direct database join to fetch only the required 'key' and 'content'.
        // This is the core optimization:
        // - `join`: Directly links translations to their keys in the database.
        // - `where`: Filters by the specific locale ID.
        // - `select`: Retrieves only the 'key' from `translation_keys` and 'content' from `translations`.
        // - `get()`: Fetches results as a simple collection of stdClass objects, which are lighter than Eloquent models.
        $translations = DB::table('translations as t')
            ->join('translation_keys as tk', 't.translation_key_id', '=', 'tk.id')
            ->where('t.locale_id', $localeId)
            ->select('tk.key', 't.content')
            ->get();

        // Step 3: Transform the flat collection of objects into the desired associative array format.
        // This transformation is now applied to a much smaller and lighter dataset in memory,
        // as the heavy lifting (filtering, joining, selecting) was done by the database.
        return $translations->mapWithKeys(fn($item) => [$item->key => $item->content])->toArray();
    }

    /**
     * Handle the HTTP request for exporting translations.
     * Delegates to the handle method and returns a JSON response.
     *
     * @param Request $request The incoming HTTP request.
     * @return JsonResponse The JSON response containing the translations or an error message.
     */
    public function asController(Request $request): JsonResponse
    {
        // Retrieve the 'locale' parameter from the request.
        $locale = $request->get('locale');

        // Validate that the 'locale' parameter is provided.
        if (!$locale) {
            return response()->json(['message' => 'Locale is required.'], 422);
        }

        // Call the core 'handle' method to retrieve the translations.
        $translations = $this->handle($locale);

        // Return the fetched translations as a JSON response.
        return response()->json($translations);
    }
}
