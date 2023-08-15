<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Product;
use App\Models\Discount;

class OrderController extends Controller
{

    /**
     * Calculate the receipt for the user's basket.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateReceipt(Request $request)
    {
        $user_id = $request->user()->id;

        $basket = Redis::lrange("basket:$user_id", 0, -1);

        if ($basket) {
            $totalPrice = 0;
            $purchases = [];
            $discountedAmount = 0;

            // Count the occurrences of each value in the array
            $counts = array_count_values($basket);

            foreach ($counts as $product_id => $quantity) {
                $product_price = $this->getProductPrice($product_id);

                $purchases[] = [
                    'product_id' => $product_id,
                    'price' => $product_price,
                    'qty' => $quantity
                ];
            }

            // Calculate the total price
            foreach ($purchases as $purchase) {
                $totalPrice += $purchase['price'] * $purchase['qty'];
            }

            // Calculate the discounted amount
            foreach ($purchases as $purchase) {
                $product_id = $purchase['product_id'];
                $discount = Discount::where('product_id', $product_id)->first();

                if ($discount && $discount->is_active) {
                    $buy_quantity = $discount->buy_quantity;
                    $regularPrice = $purchase['price'];
                    $totalItems = $purchase['qty'];

                    $sets = (int) ($totalItems / $buy_quantity); // Determine the number of sets of three items
                    $remainder = $totalItems % $buy_quantity; // Calculate the remainder (additional items)

                    $totalDiscountedPrice = $sets * $buy_quantity * ($regularPrice * ($discount->percentage / 100)); // Calculate the total discounted price for completed sets

                    $discountedAmount += $totalDiscountedPrice; // Calculate the total discounted amount

                }
            }

            // Calculate the final total price after applying discounts
            $finalTotalPrice = $totalPrice - $discountedAmount;

            // // Prepare the receipt data
            $receipt = [
                'total_price' => $totalPrice,
                'discounted_amount' => $discountedAmount,
                'final_total_price' => $finalTotalPrice,
            ];

            return response()->api($receipt);
        }

        return response()->error('Basket is empty', 404);
    }

    /**
     * Get the price of a product from the database or any other source.
     *
     * @param int $product_id
     * @return float
     */
    private function getProductPrice($product_id)
    {
        $product = Product::find($product_id);

        if ($product) {
            $product_price = $product->price;
            // price need to be divided by 100 to be displayed as 9.99 instead of 999
            $product_price = $product_price / 100;

            return $product_price;
        }

        // Return a default price if the product is not found
        return 0.0;
    }
}
