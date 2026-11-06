<?php

namespace Tests\Feature;

use Tests\TestCase;

class InventoryValidationTest extends TestCase
{
    public function test_zero_stock_movement_quantity_is_rejected_with_api_envelope(): void
    {
        $response = $this->postJson('/api/stock-in', ['quantity' => 0]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('data', null)
            ->assertJsonValidationErrors(['product_id', 'quantity']);
    }

    public function test_negative_product_price_is_rejected(): void
    {
        $response = $this->postJson('/api/products', ['unit_price' => -1]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['unit_price', 'category_id', 'supplier_id']);
    }

    public function test_negative_initial_stock_is_rejected(): void
    {
        $response = $this->postJson('/api/products', ['stock_quantity' => -1]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['stock_quantity', 'category_id', 'supplier_id']);
    }
}
