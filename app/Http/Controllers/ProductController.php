<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Create product
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validate =  validator($request->all(), [
            'name' => 'required',
            'stock' => 'required',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validate->fails()) {
            return response()->error($validate->errors(), 400);
        }

        $product = Product::create([
            'name' => $request->input('name'),
            'desc' => $request->input('desc'),
            'stock' => $request->input('stock'),
            'price' => $request->input('price') * 100, //convert input from 9.99 to 999
        ]);

        return response()->api(
            [
                $product
            ]
        );
    }

    /**
     * Remove product
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->api(
            []
        );
    }
}
