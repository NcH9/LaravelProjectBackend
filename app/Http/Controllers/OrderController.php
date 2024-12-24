<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index() {
        return Order::all();
    }
    public function show(Request $request) {
        $validated = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);
        $order = Order::where('room_id', $validated['room_id'])
        ->where('user_id', $validated['user_id'])->first();
        
        return response()->json($order);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);
        Order::create($validated);
        return response()->json(['message' => 'Order created'], 201);
    }
}
