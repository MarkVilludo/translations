<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Actions\Translations\CreateTranslation;
use App\Actions\Translations\UpdateTranslation;
use App\Actions\Translations\GetTranslation;
use App\Actions\Translations\SearchTranslations;
use App\Actions\Translations\ExportTranslations;
use App\Actions\Auth\LoginAction;


Route::post('/login', [LoginAction::class, 'asController']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('translations')->group(function () {
        Route::post('/', [CreateTranslation::class, 'asController']);
        Route::put('/key/{key}', UpdateTranslation::class, 'asController');
        Route::get('/key/{key}', [GetTranslation::class, 'asController']);
        Route::get('/', [SearchTranslations::class, 'asController']);
        Route::get('/exports', [ExportTranslations::class, 'asController']);

    });
});
