<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_successful_response(): void
    {
        $response = $this->get('/api/wrong_route');
        $response->assertStatus(404);

        $response = $this->get('/api/users');
        $response->assertStatus(200);

        $response = $this->get('/api/products');
        $response->assertStatus(200);

        $response = $this->get('/api/orders');
        $response->assertStatus(200);

        $response = $this->get('/api/payments');
        $response->assertStatus(200);
    }

    public function test_general_routes_return_expected_and_valid_json(): void
    {
        $this->seed();

        $response = $this->get('/api/users');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json->has(10)->first(fn(AssertableJson $json) => $json->hasAll('id', 'username')
            ->whereType('id', 'integer')
            ->whereType('username', 'string')
            ->etc()
        )

        );

        $response = $this->get('/api/products');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json->has(20)
            ->first(fn(AssertableJson $json) =>
                $json->hasAll('id', 'name', 'price', 'inventory')
                    ->whereType('id', 'integer')
                    ->whereType('name', 'string')
                    ->whereType('price', 'integer')
                    ->whereType('inventory', 'integer')
                    ->etc()
            )
        );
    }

    public function test_orders_and_payment_routes_return_expected_and_valid_json(): void
    {
        $this->seed();
        $products = json_decode($this->get('/api/products')->getContent());
        $response = $this->get('/api/users');
        $firstUser = json_decode($response->getContent())[0];
        $response = $this->postJson('/api/orders/reserve'
            ,[
                "ipg" => "ipga",
                "user_id" => $firstUser->id,
                "username" => $firstUser->username,
                "items" => [
                    [
                        "product_id" => $products[0]->id,
                        "quantity" => 10
                    ],
                    [
                        "product_id" => $products[1]->id,
                        "quantity" => 3
                    ]
                ]
            ]
        );
        $response->assertStatus(200);


        $response = $this->get('/api/orders');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json->has(1)
            ->first(fn(AssertableJson $json) =>
                $json->hasAll('id', 'user_id', 'status')
                    ->whereType('id', 'integer')
                    ->whereType('status', 'string')
                    ->whereType('user_id', 'integer')
                    ->has('order_items', null, fn(AssertableJson $json) =>
                        $json->hasAll('id', 'order_id', 'product_id',
                            'user_id', 'quantity')
                            ->whereType('id', 'integer')
                            ->whereType('order_id', 'integer')
                            ->whereType('product_id', 'integer')
                            ->whereType('user_id', 'integer')
                            ->whereType('quantity', 'integer')
                            ->etc()
                        )->etc()
            )
        );

        $response = $this->get('/api/payments');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json->has(1)
            ->first(fn(AssertableJson $json) =>
            $json->hasAll('id', 'order_id', 'user_id', 'amount', 'gateway_type', 'status')
                ->whereType('id', 'integer')
                ->whereType('order_id', 'integer')
                ->whereType('user_id', 'integer')
                ->whereType('gateway_type', 'string')
                ->whereType('status', 'string')
                ->etc()
            )
        );
    }

}
