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

    public function index($id)
    {
        $orders = DB::select('SELECT o.*, p.product_name, p.price, p.img 
        FROM Orders o 
        JOIN products p ON p.id = o.product_id 
        JOIN users u ON o.user_id = u.id 
        WHERE u.id = ?', [$id]);

        if (count($orders) > 0) {
            foreach ($orders as $order) {
                $order->img = asset('storage/' . $order->img);
            }
            return response()->json(["data" => $orders]);
        } else {
            return response()->json(["data" => []]); // Return empty array if no orders found
        }
    }

    
    public function admin_index()
    {
        $orders = DB::select('SELECT o.*, p.product_name, p.price, p.img , u.email  as client
        FROM Orders o 
        JOIN products p ON p.id = o.product_id 
        JOIN users u ON o.user_id = u.id ');

        if (count($orders) > 0) {
            foreach ($orders as $order) {
                $order->img = asset('storage/' . $order->img);
            }
            return response()->json(["data" => $orders]);
        } else {
            return response()->json(["data" => []]); // Return empty array if no orders found
        }
    }

    // ---------------------------------------------------------------------------------------

    public function show($id)
    {
        $order = DB::select('SELECT o.* , p.product_name, p.img , p.description , p.price , p.quantity as product_quantity  FROM Orders o JOIN products p ON p.id = o.product_id WHERE o.id = :id', [$id])[0];
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->img = $order->img ? asset('storage/' . $order->img) : asset('storage/default.png'); // Ensure default.png is accessible

        return response()->json($order);
    }

    // ---------------------------------------------------------------------------------------
    public function store(Request $request)
    {
        try {

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

            return response()->json(['message' => 'order created successfuly', 'order' => $order], 201);
        } catch (ValidationException $e) {

            return response()->json(['errors' => $e->errors()], 201);
        }
    }
    // ---------------------------------------------------------------------------------------
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        try {
            $validation = $request->validate([
                'quantity' => 'required|numeric|min:1',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $order->quantity = $validation['quantity'];
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
