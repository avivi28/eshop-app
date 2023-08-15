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

        // Delete the user's basket in Redis
        Redis::del("basket:$user_id");
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

        // Delete the user's basket in Redis
        Redis::del("basket:$user_id");
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

        // Delete the user's basket in Redis
        Redis::del("basket:$user_id");
    }

    /**
     * Test calculateReceipt method with discounts.
     *
     * @return void
     */
    public function testCalculateReceiptWithDiscounts()
    {
        // Create a user with role user
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        // Get user token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Get user id
        $user_id = $user->id;

        // Create sample products
        $product1 = Product::factory()->create(['price' => 1000]); // Price: $10.00
        $product2 = Product::factory()->create(['price' => 500]); // Price: $5.00
        $product3 = Product::factory()->create(['price' => 800]); // Price: $8.00

        // Create a discount for product1 (buy two, get one 50% off)
        $discount = Discount::factory()->create([
            'product_id' => $product1->id,
            'buy_quantity' => 2,
            'percentage' => 50,
            'is_active' => true
        ]);

        $discount = Discount::factory()->create([
            'product_id' => $product2->id,
            'buy_quantity' => 2,
            'percentage' => 50,
            'is_active' => true
        ]);

        // Delete the user's basket in Redis
        Redis::del("basket:$user_id");

        // Add products to the user's basket in Redis
        Redis::lpush("basket:{$user_id}", $product1->id);
        Redis::lpush("basket:{$user_id}", $product1->id); // This item should get the discount
        Redis::lpush("basket:{$user_id}", $product1->id); // This item should get the discount

        Redis::lpush("basket:{$user_id}", $product2->id);
        Redis::lpush("basket:{$user_id}", $product2->id);
        Redis::lpush("basket:{$user_id}", $product2->id); // This item should not get the discount

        Redis::lpush("basket:{$user_id}", $product3->id);
        Redis::lpush("basket:{$user_id}", $product3->id); // This item should not get the discount

        // Make a request to calculate the receipt
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user/orders');

        // Assert the response data
        $response->assertStatus(200);

        $responseData = json_decode($response->getContent(), true);

        $expectedData = [
            "total_price" => 61,
            "discounted_amount" => 15,
            "final_total_price" => 46
        ];

        $this->assertEquals($expectedData, $responseData['data']);

        // Delete the user's basket in Redis
        Redis::del("basket:$user_id");
    }
}
