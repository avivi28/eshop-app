<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DiscountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//login first to check if user is admin or not
Route::post('/login', [AuthController::class, 'login']);

Route::group(['prefix' => 'user'], function () {
    Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
        Route::get('/baskets', [BasketController::class, 'getBasket']);
        Route::post('/baskets', [BasketController::class, 'add']);
        Route::delete('/baskets/{id}', [BasketController::class, 'remove']);

        Route::get('/orders', [OrderController::class, 'calculateReceipt']);
    });
});

Route::group(['prefix' => 'admin'], function () {
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/products', [ProductController::class, 'create']);
        Route::delete('/products/{id}', [ProductController::class, 'remove']);

        Route::post('/discounts', [DiscountController::class, 'create']);
    });
});
