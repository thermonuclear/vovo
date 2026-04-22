<?php

use Illuminate\Support\Facades\Route;

Route::prefix('docs')->group(function () {
    Route::view('/', 'docs.swagger')->name('docs');
    Route::get('/openapi.json', function () {
        return response()->file(base_path('docs/openapi.json'), [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    })->name('docs.openapi');
});
