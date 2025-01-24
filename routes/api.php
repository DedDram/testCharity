<?php

use App\Http\Controllers\CharityProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/charity-projects', [CharityProjectController::class, 'index']);
    Route::get('/charity-projects/{slug}', [CharityProjectController::class, 'show']);
    Route::post('/donate', [CharityProjectController::class, 'donate']);
});
