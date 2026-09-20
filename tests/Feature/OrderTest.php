<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created_successfully(): void
    {
        $product = Product::factory()->create([
            'price' => 100,
            'tax_percentage' => 18,
            'stock_on_hand' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Order created successfully.',
            ]);

        $this->assertDatabaseHas('orders', [
            'customer_id' => Customer::where(
                'email',
                'test@example.com'
            )->value('id'),
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 8,
        ]);
    }

    public function test_order_fails_when_stock_is_insufficient(): void
    {
        $product = Product::factory()->create([
            'price' => 100,
            'tax_percentage' => 18,
            'stock_on_hand' => 1,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);

        $this->assertDatabaseCount('orders', 0);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 1,
        ]);
    }

    public function test_stock_cannot_be_oversold(): void
    {
        $product = Product::factory()->create([
            'price' => 100,
            'tax_percentage' => 18,
            'stock_on_hand' => 1,
        ]);

        $firstResponse = $this->postJson('/api/orders', [
            'customer_name' => 'Customer One',
            'customer_email' => 'customer1@example.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $firstResponse->assertStatus(201);

        $secondResponse = $this->postJson('/api/orders', [
            'customer_name' => 'Customer Two',
            'customer_email' => 'customer2@example.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $secondResponse->assertStatus(422)
            ->assertJsonValidationErrors(['items']);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 0,
        ]);

        $this->assertDatabaseCount('orders', 1);
    }
}