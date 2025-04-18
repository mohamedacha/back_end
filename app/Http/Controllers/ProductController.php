<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $product->img = asset('storage/' . $product->img);
        }
        return response()->json([
            'data' => $products
        ]);
    }

    //---------------------------------------------------------------------------------

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $default = asset('storage/products_imgs/default.png');
        $product->img = $product->img ? asset('storage/' . $product->img) : asset('storage/default.png'); // Ensure default.jpeg is accessible
        return response()->json(['data' => $product, "default_img" => $default]);
    }
    //---------------------------------------------------------------------------------

    public function store(Request $request)
    {
        try {


            $product_validation = $request->validate([
                'product_name' => 'string|min:2|required|filled',
                'price' => 'numeric|min:1|required',
                'category' => 'required|filled',
                'description' => 'required|max:300|filled',
                'quantity' => 'numeric|required|min:1',
                'img' => 'nullable|mimes:png,jpg,jpeg|max:2048'
            ]);

            if ($request->hasFile('img')) {
                $path = $request->file('img')->store('products_imgs', 'public');
            } else {
                $path = 'products_imgs/default.png';
            }

            $product = Product::create([
                'product_name' => $product_validation['product_name'],
                'price' => $product_validation['price'],
                'category' => $product_validation['category'],
                'description' => $product_validation['description'],
                'quantity' => $product_validation['quantity'],
                'img' => $path,
            ]);
            return response()->json([
                'data' => $product
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    //---------------------------------------------------------------------------------
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['errors' => 'product not found']);
        }
        try {

            $product_validation = $request->validate([
                'product_name' => 'string|min:3|nullable',
                'price' => 'numeric|min:0|nullable',
                'category' => 'nullable',
                'description' => 'nullable',
                'quantity' => 'numeric|nullable',
                'img' => 'nullable|mimes:png,jpg,jpeg|max:2048'
            ]);

            if ($request->hasFile('img')) {
                if ($product->img && $product->img !== 'products_imgs/default.png' && Storage::disk('public')->exists($product->img)) {
                    Storage::disk('public')->delete($product->img);
                }
                $path = $request->file('img')->store('products_imgs', 'public');
            } else {
                $path = $product->img;
                // $path = 'products_imgs/default.png';
            }

            $product->product_name = $product_validation['product_name'] ?? $product->product_name;
            $product->price = $product_validation['price'] ?? $product->price;
            $product->category = $product_validation['category'] ?? $product->category;
            $product->description = $product_validation['description'] ?? $product->description;
            $product->quantity = $product_validation['quantity'] ?? $product->quantity;
            $product->img = $path;
            $product->save();

            return response()->json([
                'message' => 'the product updated successfuly',
                'data' => $product
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    //---------------------------------------------------------------------------------
    public function destroy($id)
    {
        $product = Product::Find($id);
        if (!$product) {
            return response()->json(['message' => 'product not found'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'product deleted successfuly'], 200);
    }
}
