<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
            'img' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        if($request->hasFile('img')){
            $name_img = now()->format('Y-m-d_H-i-s')  .'_'. $request->file('img')->getClientOriginalName() ;
            $request->file('img')->move(public_path('img/products_imgs') , $name_img);
        }else{
            $name_img = 'default.png' ;
        }

        $product = Product::create([
        'product_name' => $product_validation['product_name'],
        'price' => $product_validation['price'],
        'category'=> $product_validation['category'],
        'description'=> $product_validation['description'],
        'quantity' => $product_validation['quantity'],
        'img' => $name_img,
        ]);
        return response()->json([
            'data' => $product
        ]);
    }
//---------------------------------------------------------------------------------
    public function update(Request $request , $id){
        $product = Product::findOrFail($id);

        $product_validation = $request->validate([
            'product_name' => 'min:1|required',
            'price' => 'numeric|min:0|required',
            'category' => 'required' ,
            'description' => 'nullable',
            'quantity' =>'numeric|required' ,
            'img' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        if($request->hasFile('img')){
            $name_img = now()->format('Y-m-d_H-i-s')  .'_'. $request->file('img')->getClientOriginalName() ;
            $request->file('img')->move(public_path('img/products_imgs') , $name_img);
        }else{
            $name_img = 'default.png' ;
        }

        $product->product_name = $product_validation['product_name'];
        $product->price = $product_validation['price'];
        $product->category = $product_validation['category'];
        $product->description = $product_validation['description'];
        $product->quantity = $product_validation['quantity'];
        $product->img = $name_img ;
        $product->save();

        return response()->json([
            'message' =>'the product updated successfuly',
            'data' => $product
        ]);
    }
//---------------------------------------------------------------------------------
    public function delete($id){
        $product = Product::FindOrFail($id);
        $product->delete();
        return response()->json(['message' =>'the product deleted successfuly'] , 200);
    }
}
