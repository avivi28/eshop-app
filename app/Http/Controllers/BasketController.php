<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class BasketController extends Controller
{
    /**
     * Add a product to the user's basket.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $userId = $request->user()->id;

        Redis::sadd("basket:$userId", $productId);

        return response()->api([]);
    }

    /**
     * Remove a product from the user's basket.
     *
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request, $productId)
    {
        $userId = $request->user()->id;

        Redis::srem("basket:$userId", $productId);

        return response()->api([]);
    }

    /**
     * Get the user's basket.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBasket(Request $request)
    {
        $userId = $request->user()->id;

        $basket = Redis::smembers("basket:$userId");

        return response()->api($basket);
    }
}
