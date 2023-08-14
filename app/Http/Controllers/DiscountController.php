<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Create discount
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validate =  validator($request->all(), [
            'buy_quantity' => 'numeric|min:1',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ]);

        if ($validate->fails()) {
            return response()->error($validate->errors(), 400);
        }

        $discount = Discount::create([
            'name' => $request->input('name'),
            'desc' => $request->input('desc'),
            'buy_quantity' => $request->input('buy_quantity'),
            'percentage' => $request->input('percentage'),
            'is_active' => $request->input('is_active'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);

        return response()->api(
            [
                $discount
            ]
        );
    }
}
