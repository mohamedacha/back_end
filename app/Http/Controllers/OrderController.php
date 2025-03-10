<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    // ---------------------------------------------------------------------------------------

    public function index()
    {
        $orders = Order::all();
        return response()->json($orders);
    }

    // ---------------------------------------------------------------------------------------

    public function show($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order);
    }

    // ---------------------------------------------------------------------------------------
    public function store(Request $request)
    {
        $order_validate = $request->validate([
            'quantity' => 'numeric|required',
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

        $order_validate = $request->validate([
            'quantity' => 'numeric|required',
        ]);

        $order->quantity = $order_validate['quantity'];
        $order->save();
        return response()->json($order);
    }

    // Delete an order--------------------------------------------------
    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->delete();
        return response()->json(['message' => 'Order deleted']);
    }
}
