<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BasketController;

Route::group(['prefix' => 'user'], function () {
    Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
        Route::get('/baskets', [BasketController::class, 'getBasket']);
        Route::post('/baskets', [BasketController::class, 'add']);
        Route::delete('/baskets/{id}', [BasketController::class, 'remove']);
    });
});
