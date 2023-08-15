<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Discount;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Redis;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function testAddToBasket()
    {
        // Create a user with role user
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        // Get user token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Get user id
        $user_id = $user->id;

        // Create a product
        $product = Product::factory()->create();

        // Make a request to add the product to the user's basket
        $response = $this->postJson('/api/v1/user/baskets', ['product_id' => $product->id], ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(200);

        // Assert that the product is added to the user's basket in Redis
        $basket = Redis::lrange("basket:$user_id", 0, -1);
        $this->assertTrue(in_array($product->id, $basket));
    }

    public function testRemoveFromBasket()
    {
        // Create a user with role user
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        // Get user token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Get user id
        $user_id = $user->id;

        // Create a product
        $product = Product::factory()->create();

        // Add the product to the user's basket in Redis
        Redis::rpush("basket:{$user_id}", $product->id);

        // Make a request to remove the product from the user's basket with bearer token
        $response = $this->deleteJson("/api/v1/user/baskets/{$product->id}", [], ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(200);

        // Assert that the product is removed from the user's basket in Redis
        $basket = Redis::lrange("basket:$user_id", 0, -1);
        $this->assertFalse(in_array($product->id, $basket));
    }

    public function testGetBasket()
    {
        // Create a user with role user
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        // Get user token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Get user id
        $user_id = $user->id;

        // Add some products to the user's basket in Redis
        Redis::rpush("basket:{$user_id}", 1);
        Redis::rpush("basket:{$user_id}", 2);

        // Make a request to get the user's basket
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user/baskets');

        // dd($response);
        $response->assertStatus(200);

        // Assert the response JSON structure and the presence of the added products
        $response->assertJson([
            'data' => [1, 2],
        ]);
    }

    // public function testCalculateReceipt()
    // {
    //     // Create a user and authenticate
    //     $user = User::factory()->create();
    //     Sanctum::actingAs($user);

    //     // Add some products to the user's basket in Redis
    //     Redis::sadd("basket:{$user->id}", 1);
    //     Redis::sadd("basket:{$user->id}", 2);

    //     // Create a discount
    //     $discount = Discount::factory()->create([
    //         'is_active' => true,
    //         'buy_quantity' => null,
    //     ]);

    //     // Configure the mocked getProductPrice method to return a fixed price for testing
    //     $this->mock(Product::class)
    //         ->shouldReceive('find')
    //         ->andReturnUsing(function ($productId) {
    //             // Return a mock product with a fixed price for testing
    //             return new Product(['id' => $productId, 'price' => 10.0]);
    //         });

    //     // Configure the mocked Discount model to return the created discount for testing
    //     $this->mock(Discount::class)
    //         ->shouldReceive('where')
    //         ->with('is_active', true)
    //         ->once()
    //         ->andReturnSelf()
    //         ->shouldReceive('where')
    //         ->with('buy_quantity', null)
    //         ->once()
    //         ->andReturnSelf()
    //         ->shouldReceive('get')
    //         ->once()
    //         ->andReturn(collect([$discount]));

    //     // Make a request to calculate the receipt
    //     $response = $this->get('/user/receipt');

    //     $response->assertStatus(200);

    //     // Assert the response JSON structure and the calculated receipt values
    //     $response->assertJson([
    //         'data' => [
    //             'total_price' => 20.0,
    //             'applied_discounts' => [$discount->name],
    //             'discounted_amount' => 4.0,
    //             'final_total_price' => 16.0,
    //         ],
    //     ]);
    // }
}
