<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
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
            $product
        );
    }

    public function remove($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product removed successfully']);
    }

    public function addDiscount(Request $request, $id)
    {
        $request->validate([
            'discount' => 'required|numeric|min:0|max:100',
        ]);

        $product = Product::findOrFail($id);
        $product->discount = $request->input('discount');
        $product->save();

        return response()->json($product);
    }
}
