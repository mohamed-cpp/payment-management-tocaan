<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $query = Order::with('items')->where('user_id', auth('api')->id());


        # filters
        if ($request->has('id')) {
            $query->where('id', $request->id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = Order::create([
            'user_id' => auth('api')->id(),
            'status' => Order::STATUS_PENDING,
            'total' => 0,
        ]);

        foreach ($request->items as $item) {
            $order->items()->create([
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        $order->total = $order->calculateTotal();
        $order->save();

        $order->load('items');

        return response()->json([
            'message' => 'Order created successfully.',
            'order' => $order,
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        if (! auth()->user()->can('view', $order)) {
            return response()->json(['message' => 'Order not found',], 404);
        }
        // $this->authorize('view', $order);

        $order->load(['items', 'payments']);

        return response()->json($order);
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        if (! auth()->user()->can('update', $order)) {
            return response()->json(['message' => 'Order not found',], 404);
        }

        if ($order->hasPayments()) {
            return response()->json([
                'message' => 'The order has payment.',
            ], 422);
        }

        if ($request->has('items')) {
            $order->items()->delete();
            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
            $order->total = $order->calculateTotal();
        }

        $order->save();
        $order->load('items');

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order,
        ]);
    }


    public function destroy(Order $order): JsonResponse
    {
        if (! auth()->user()->can('delete', $order)) {
            return response()->json(['message' => 'Order not found',], 404);
        }

        if ($order->hasPayments()) {
            return response()->json([
                'message' => 'The order hasnot payments.',
            ], 422);
        }

        $order->items()->delete();
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }
}
