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
        $user_id = $request->user()->id;

        Redis::rpush("basket:$user_id", $productId);

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
        $user_id = $request->user()->id;
        $basketKey = "basket:$user_id";

        $removedCount = Redis::lrem($basketKey, 1, $productId);

        return response()->api(['removed_count' => $removedCount]);
    }

    /**
     * Get the user's basket.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBasket(Request $request)
    {
        $user_id = $request->user()->id;

        $basket = Redis::lrange("basket:$user_id", 0, -1);

        return response()->api($basket);
    }
}
