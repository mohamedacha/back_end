<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    // ---------------------------------------------------------------------------------------

    public function index()
    {
        // $orders = Order::all();
        $orders = DB::select('SELECT o.* , p.product_name , p.price FROM Orders o JOIN products p ON p.id = o.product_id ' );
        return response()->json(["data" => $orders]);
    }

    // ---------------------------------------------------------------------------------------

    public function show($id)
    {
        $order = DB::select('SELECT o.* , p.product_name, p.img , p.description , p.price   FROM Orders o JOIN products p ON p.id = o.product_id WHERE o.id = :id' ,[$id] )[0];
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order);
    }

    // ---------------------------------------------------------------------------------------
    public function store(Request $request)
    {
        $order_validate = $request->validate([
            'quantity' => 'required|numeric|min:1',
            'product_id' => 'numeric|nullable',
            'service_id' => 'numeric|nullable',
            'user_id' => 'numeric|required',
        ]);

        $order = Order::create([
            'confirmed' => false,
            'quantity' => $order_validate['quantity'],
            'product_id' => $order_validate['product_id'] ?? null,
            'service_id' => $order_validate['service_id'] ?? null,
            'user_id' => $order_validate['user_id'],
        ]);

        return response()->json(['data' => $order], 201);
    }
    // ---------------------------------------------------------------------------------------
    public function update(Request $request, $id)
{
    $order = Order::find($id);
    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }
    try{
        $validation = $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

    }catch(ValidationException $e){
        return response()->json(['errors' => $e->errors()], 422);
    }

    $order->quantity = $validation['quantity'] ;
    $order->save();

    return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
}


    // Delete an order--------------------------------------------------
    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->delete();
        return response()->json(['message' => 'Order deleted successfuly']);
    }

    public function confirmOrder($id): JsonResponse
{
    $order = Order::find($id);

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    // Update the order confirmation status
    $order->update(['confirmed' => true]);

    return response()->json(['message' => 'Order confirmed successfully'], 200);
}
}
