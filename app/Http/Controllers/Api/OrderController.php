<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->validated()
        );

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }

    public function customerHistory(
        \Illuminate\Http\Request $request
    ): \Illuminate\Http\JsonResponse {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);
    
        $customer = \App\Models\Customer::where(
            'email',
            $request->email
        )->first();
    
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found.',
            ], 404);
        }
    
        $orders = $customer->orders()
            ->with([
                'items.product',
            ])
            ->latest()
            ->get();
    
        return response()->json([
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'orders' => $orders,
        ]);
    }

    public function lowStock(
    \Illuminate\Http\Request $request
    ): \Illuminate\Http\JsonResponse {
        $threshold = $request->input('threshold', 10);

        if (!is_numeric($threshold) || $threshold < 0) {
            return response()->json([
                'message' => 'Threshold must be a non-negative number.',
            ], 422);
        }

        $products = \App\Models\Product::where(
            'stock_on_hand',
            '<',
            $threshold
        )
            ->orderBy('stock_on_hand')
            ->get();

        return response()->json([
            'threshold' => (int) $threshold,
            'products' => $products,
        ]);
    }
}