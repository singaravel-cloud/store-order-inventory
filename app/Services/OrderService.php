<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $customer = Customer::firstOrCreate(
                [
                    'email' => $data['customer_email'],
                ],
                [
                    'name' => $data['customer_name'],
                ]
            );

            $subtotal = 0;
            $taxTotal = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {

                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => [
                            'One of the selected products was not found.',
                        ],
                    ]);
                }

                if ($product->stock_on_hand < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Insufficient stock for product: {$product->name}.",
                        ],
                    ]);
                }

                $quantity = $item['quantity'];

                $itemSubtotal = $product->price * $quantity;

                $itemTax = $itemSubtotal
                    * ($product->tax_percentage / 100);

                $itemTotal = $itemSubtotal + $itemTax;

                $subtotal += $itemSubtotal;
                $taxTotal += $itemTax;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'tax_percentage' => $product->tax_percentage,
                    'subtotal' => $itemSubtotal,
                    'tax' => $itemTax,
                    'total' => $itemTotal,
                ];

                $product->decrement(
                    'stock_on_hand',
                    $quantity
                );
            }

            $grandTotal = $subtotal + $taxTotal;

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => $subtotal,
                'tax' => $taxTotal,
                'grand_total' => $grandTotal,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            SendOrderConfirmationJob::dispatch($order->id);
            
            return $order->load([
                'customer',
                'items.product',
            ]);
        });
    }
}