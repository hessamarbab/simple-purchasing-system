<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PurchasingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_validations_and_exceptions_for_reserve(): void
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

    public function test_validations_and_exceptions_for_confirm(): void
    {
        $response = $this->get('/api/orders/return_bank/ipga/MTI=?success=2',[
            "Accept" => "application/json"
        ]);
        $response->assertStatus(422);
        $response->assertExactJson([
            "message" => "The success field must be true or false.",
            "errors" => [
                "success" => [
                    "The success field must be true or false."
                ]
            ]
        ]);

        $response = $this->get('/api/orders/return_bank/ipga/MTI=',[
            "Accept" => "application/json"
        ]);
        $response->assertStatus(422);
        $response->assertExactJson([
            "message" => "The success field is required.",
            "errors" => [
                "success" => [
                    "The success field is required."
                ]
            ]
        ]);

        $response = $this->get('/api/orders/return_bank/ipga/MA==?success=1',[
            "Accept" => "application/json"
        ]);
        $response->assertStatus(404);
        $response->assertExactJson([
            'message' => "Not Found"
        ]);

        $response = $this->get('/api/orders/return_bank/wrong_string/MTI=?success=1',[
            "Accept" => "application/json"
        ]);
        $response->assertStatus(400);
    }

    public function test_complete_purchasing_senario_integration() : void
    {
        $this->seed();
        $productsBeforeReserve = json_decode($this->get('/api/products')->getContent());
        $response = $this->get('/api/users');
        $users = json_decode($response->getContent());

        $responseReserve1 = $this->postJson('/api/orders/reserve'
            ,[
                "ipg" => "ipgb",
                "user_id" => $users[0]->id,
                "username" => $users[0]->username,
                "items" => [
                    [
                        "product_id" => $productsBeforeReserve[0]->id,
                        "quantity" => 5
                    ],
                    [
                        "product_id" => $productsBeforeReserve[1]->id,
                        "quantity" => 10
                    ]
                ]
            ]
        );

        $productsAfterFirstReserve = json_decode($this->get('/api/products')->getContent());
        $this->assertTrue(
            $productsAfterFirstReserve[0]->inventory == ($productsBeforeReserve[0]->inventory - 5)
        );
        $this->assertTrue(
            $productsAfterFirstReserve[1]->inventory == ($productsBeforeReserve[1]->inventory - 10)
        );

        $responseReserve2 = $this->postJson('/api/orders/reserve'
            ,[
                "ipg" => "ipgb",
                "user_id" => $users[0]->id,
                "username" => $users[0]->username,
                "items" => [
                    [
                        "product_id" => $productsBeforeReserve[0]->id,
                        "quantity" => 7
                    ],
                    [
                        "product_id" => $productsBeforeReserve[1]->id,
                        "quantity" => 15
                    ]
                ]
            ]
        );

        $productsAfterSecondReserve = json_decode($this->get('/api/products')->getContent());
        $this->assertTrue(
            $productsAfterSecondReserve[0]->inventory == ($productsAfterFirstReserve[0]->inventory - 7)
        );
        $this->assertTrue(
            $productsAfterSecondReserve[1]->inventory == ($productsAfterFirstReserve[1]->inventory - 15)
        );

        $response = $this->get('/api/orders');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json->has(2)
            ->first(fn(AssertableJson $json) =>
            $json->hasAll('id', 'user_id', 'status')
                ->whereType('id', 'integer')
                ->whereType('status', 'string')
                ->whereType('user_id', 'integer')
                ->where('status', 'reserved')
                ->has('order_items', 2, fn(AssertableJson $json) =>
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
        $response->assertJson(fn(AssertableJson $json) => $json->has(2)
            ->first(fn(AssertableJson $json) =>
            $json->hasAll('id', 'order_id', 'user_id', 'amount', 'gateway_type', 'status')
                ->whereType('id', 'integer')
                ->whereType('order_id', 'integer')
                ->whereType('user_id', 'integer')
                ->whereType('gateway_type', 'string')
                ->whereType('status', 'string')
                ->where('status', 'pending')
                ->etc()
            )
        );

        $responseReserve1Content = json_decode($responseReserve1->getContent());
        $responseConfirm1 = $this->get($responseReserve1Content->success_url);
        $responseConfirm1->assertStatus(201);

        // inventory in success pay should not change

        $productsAfterFirstConfirm = json_decode($this->get('/api/products')->getContent());
        $this->assertTrue(
            $productsAfterFirstConfirm[0]->inventory == $productsAfterSecondReserve[0]->inventory
        );
        $this->assertTrue(
            $productsAfterFirstConfirm[1]->inventory == $productsAfterSecondReserve[1]->inventory
        );

        $response = $this->get('/api/orders');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) =>
            $json->has("0", fn (AssertableJson $json) =>
                $json->where('status','performed')
                ->etc()
            )
        );
        $response->assertJson(fn(AssertableJson $json) =>
            $json->has("1", fn (AssertableJson $json) =>
                $json->where('status','reserved')
                    ->etc()
            )
        );

        $response = $this->get('/api/payments');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) =>
            $json->has("0", fn (AssertableJson $json) =>
                $json->where('status','completed')
                    ->etc()
            )
        );
        $response->assertJson(fn(AssertableJson $json) =>
            $json->has("1", fn (AssertableJson $json) =>
                $json->where('status','pending')
                    ->etc()
            )
        );


        $responseReserve2Content = json_decode($responseReserve2->getContent());
        $responseConfirm2 = $this->get($responseReserve2Content->failed_url);
        $responseConfirm2->assertStatus(201);
        $productsAfterSecondConfirm = json_decode($this->get('/api/products')->getContent());

        // inventory in fail pay should rollback
        $this->assertTrue(
            $productsAfterSecondConfirm[0]->inventory == ($productsAfterFirstConfirm[0]->inventory + 7)
        );
        $this->assertTrue(
            $productsAfterSecondConfirm[1]->inventory == ($productsAfterFirstConfirm[1]->inventory + 15)
        );


        $response = $this->get('/api/orders');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) =>
        $json->has("0", fn (AssertableJson $json) =>
        $json->where('status','performed')
            ->etc()
        )
        );
        $response->assertJson(fn(AssertableJson $json) =>
        $json->has("1", fn (AssertableJson $json) =>
        $json->where('status','failed')
            ->etc()
        )
        );

        $response = $this->get('/api/payments');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) =>
        $json->has("0", fn (AssertableJson $json) =>
        $json->where('status','completed')
            ->etc()
        )
        );
        $response->assertJson(fn(AssertableJson $json) =>
        $json->has("1", fn (AssertableJson $json) =>
        $json->where('status','canceled')
            ->etc()
        )
        );

    }

}
