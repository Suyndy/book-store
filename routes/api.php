<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::prefix('books')->group(function() {
    Route::get('/', [BookController::class, 'getAll']);
    Route::get('/{id}', [BookController::class, 'getOne']);
    Route::post('/', [BookController::class, 'create']);
    Route::put('/{id}', [BookController::class, 'update']);
    Route::delete('/{id}', [BookController::class, 'delete']);
});