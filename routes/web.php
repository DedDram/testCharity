<?php

use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/api/documentation/{documentation?}', [SwaggerController::class, 'api']);
