<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    // List all orders
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders);
    }

    // Show a specific order
    public function show($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order);
    }

    // Create a new order
    public function store(Request $request)
    {
        $order_validate = $request->validate([
            'quantity' => 'numeric|required',
            'product_id' => 'numeric|nullable',
            'service_id' => 'numeric|nullable',
        ]);

        $order = Order::create([
            'confirmed' => false,
            'quantity' => $order_validate['quantity'],
            'product_id' => $order_validate['product_id'] ?? null,
            'service_id' => $order_validate['service_id'] ?? null,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['data' => $order], 201);
    }
      // Update an existing order
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $request->validate([
            'status' => 'in:pending,processing,completed,cancelled',
        ]);

        $order->update($request->only(['total_price', 'status']));
        return response()->json($order);
    }

    // Delete an order
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
