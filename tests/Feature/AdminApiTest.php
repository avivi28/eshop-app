<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test creating a product via API.
     *
     * @return void
     */
    public function testCreateProduct()
    {
        // Create a user with role admin
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Get user token
        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'name' => $this->faker->word,
            'stock' => $this->faker->randomNumber(2),
            'price' => $this->faker->randomNumber(2),
        ];

        $response = $this->postJson('/api/v1/admin/products', $data, ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(200);

        //$data[price] need to be multiplied by 100 to be stored in the database
        $data['price'] = $data['price'] * 100;
        $this->assertDatabaseHas('products', $data);
    }

    /**
     * Test removing a product via API.
     *
     * @return void
     */
    public function testRemoveProduct()
    {
        // Create a user with role admin
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Get user token
        $token = $user->createToken('auth_token')->plainTextToken;

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/v1/admin/products/{$product->id}", [], ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /**
     * Test creating a discount via API.
     *
     * @return void
     */
    public function testCreateDiscount()
    {
        // Create a user with role admin
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Get user token
        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'name' => $this->faker->word,
            'desc' => $this->faker->sentence,
            'buy_quantity' => $this->faker->randomNumber(2),
            'percentage' => $this->faker->randomNumber(2),
            'is_active' => $this->faker->boolean,
            'start_date' => $this->faker->date,
            'end_date' => $this->faker->date,
        ];

        $response = $this->postJson('/api/v1/admin/discounts', $data, ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('discounts', $data);
    }
}
