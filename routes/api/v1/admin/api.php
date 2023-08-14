<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::group(['prefix' => 'admin'], function () {
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/products', [ProductController::class, 'create']);
        Route::delete('/products/{id}', [ProductController::class, 'remove']);
        Route::post('/products/{id}/discount', [ProductController::class, 'addDiscount']);
    });
});
