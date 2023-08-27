<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchasingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_validation_reserve(): void
    {
        $this->seed();
        $products = json_decode($this->get('/api/products')->getContent());
        $response = $this->get('/api/users');
        $firstUser = json_decode($response->getContent())[0];
        $response = $this->postJson('/api/orders/reserve'
            , [
                "ipg" => "ipga",
                "user_id" => $firstUser->id,
                "username" => $firstUser->username,
                "items" => [
                    [
                        "product_id" => $products[19]->id + 1,
                        "quantity" => 10
                    ],
                    [
                        "product_id" => $products[19]->id + 2,
                        "quantity" => 'test'
                    ]
                ]
            ]
        );
        $response->assertStatus(422);
        $response->assertExactJson([
            "message" => "The selected items.0.product_id is invalid. (and 2 more errors)",
            "errors" => [
                "items.0.product_id" => [
                    "The selected items.0.product_id is invalid."
                ],
                "items.1.product_id" => [
                    "The selected items.1.product_id is invalid."
                ],
                "items.1.quantity" => [
                    "The items.1.quantity field must be a number."
                ]

            ]
        ]);

        $response = $this->postJson('/api/orders/reserve'
            ,[
                "ipg" => "ipga",
                "user_id" => $firstUser->id + 1,
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
        $response->assertStatus(401);

        $response = $this->postJson('/api/orders/reserve'
            ,[
                "ipg" => "wrong string",
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
        $response->assertStatus(400);

    }
}
