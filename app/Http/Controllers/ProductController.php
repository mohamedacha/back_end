<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(){
        $products = Product::all();
        return response()->json([
            'data' => $products
        ]);
    }

//---------------------------------------------------------------------------------

    public function show($id){
        $product = Product::findOrFail($id);
        return response()->json([
            'data' => $product
        ]);
    }
//---------------------------------------------------------------------------------

    public function store(Request $request){
        
        $product_validation = $request->validate([
            'product_name' => 'min:1|required',
            'price' => 'numeric|min:0|required',
            'category' => 'required' ,
            'description' => 'nullable',
            'quantity' =>'numeric|required' ,
            'img' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        if($request->hasFile('img')){

            $path = $request->file('img')->store('products_imgs' , 'public') ;
            // $name_img = now()->format('Y-m-d_H-i-s')  .'_'. $request->file('img')->getClientOriginalName() ;
            // $request->file('img')->move(public_path('img/products_imgs') , $name_img);
        }else{
            $path = 'default.png' ;
        }

        $product = Product::create([
        'product_name' => $product_validation['product_name'],
        'price' => $product_validation['price'],
        'category'=> $product_validation['category'],
        'description'=> $product_validation['description'],
        'quantity' => $product_validation['quantity'],
        'img' => $path,
        ]);
        return response()->json([
            'data' => $product
        ]);
    }
//---------------------------------------------------------------------------------
    public function update(Request $request , $id){
        $product = Product::findOrFail($id);

        $product_validation = $request->validate([
            'product_name' => 'string|min:3|nullable',
            'price' => 'numeric|min:0|nullable',
            'category' => 'nullable' ,
            'description' => 'nullable',
            'quantity' =>'numeric|nullable' ,
            'img' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        if($request->hasFile('img')){
            if ($product->img !== 'default.png') {
                Storage::disk('public')->delete($product->img);
            }
            $path = $request->file('img')->store('products_imgs','public');
            // $name_img = now()->format('Y-m-d_H-i-s')  .'_'. $request->file('img')->getClientOriginalName() ;
            // $request->file('img')->move(public_path('img/products_imgs') , $name_img);
        }else{
            $path = 'default.png' ;
        }

        $product->product_name = $product_validation['product_name'] ?? $product->product_name;
        $product->price = $product_validation['price'] ?? $product->price;
        $product->category = $product_validation['category'] ?? $product->category;
        $product->description = $product_validation['description'] ?? $product->description;
        $product->quantity = $product_validation['quantity'] ?? $product->quantity;
        $product->img = $path ;
        $product->save();

        return response()->json([
            'message' =>'the product updated successfuly',
            'data' => $product
        ]);
    }
//---------------------------------------------------------------------------------
    public function destroy($id){
        $product = Product::Find($id);
        if (!$product) {
            return response()->json(['message' => 'product not found'], 404);
        }

        $product->delete();
        return response()->json(['message' =>'product deleted successfuly'] , 200);
    }
}
