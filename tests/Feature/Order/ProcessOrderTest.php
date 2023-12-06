<?php

namespace Tests\Feature\Order;

use App\Events\IngredientLowStock;
use App\Models\Ingredient;
use App\Models\Order;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    Artisan::call('migrate:fresh');
    $seeder = new DatabaseSeeder();
    $seeder->run();
});


it('fails to create an order if payload contains invalid in this case:(product_id)', function () {
    $orderData = [
        'products' => [
            [
                'product_ids' => 15,
                'quantity' => 2,
            ],
        ],
    ];
    $response = $this->postJson('api/process-order', $orderData);
    $response->assertStatus(422);

});

it('can create a new order with multiple products', function () {
    $orderData = [
        'products' => [
            [
                'product_id' => 1,
                'quantity' => 5,
            ],
            [
                'product_id' => 2,
                'quantity' => 7,
            ],
        ],
    ];
    $this->postJson('api/process-order', $orderData);
    $order = Order::find(1);
    expect($order->id)->toEqual(1);
    expect($order->products->pluck('id')->toArray())->toEqual([1,2]);

});

it('updates ingredient amount after order creation', function () {
    // 
    $orderData = [
        'products' => [
            [
                'product_id' => 1, //['Beef'=>150,'Cheese'=>30,'Onion'=>20]
                'quantity' => 5,    //['Beef'=>750,'Cheese'=>150,'Onion'=>100]
            ],
            [
                'product_id' => 2, //['Beef'=>100,'Cheese'=>60,'Onion'=>10]
                'quantity' => 7,   //['Beef'=>700,'Cheese'=>420,'Onion'=>70]
            ],
        ],
    ];
    // seed   ['Beef'=>20000,'Cheese'=>5000,'Onion'=>1000];
    // need   ['Beef'=>1450,'Cheese'=>570,'Onion'=>170]
    // expect ['Beef'=>18550,'Cheese'=>4430,'Onion'=>830]

    $this->postJson('api/process-order', $orderData);
    $ingredients = Ingredient::whereIn('id', [1, 2, 3])->get();

    expect($ingredients->pluck('amount_in_stock')->toArray())
    ->toEqual([18550,4430,830]);
    
});

it('fails to create an order if any ingredient is not available in stock', function () {
    $onion = Ingredient::find(3);
    $onion->amount_in_stock = 40;
    $onion->save();

    $orderData = [
        'products' => [
            [
                'product_id' => 1, //['Beef'=>150,'Cheese'=>30,'Onion'=>20]
                'quantity' => 2,    
            ],
            [
                'product_id' => 2, //['Beef'=>100,'Cheese'=>60,'Onion'=>10]
                'quantity' => 1,   
            ],
        ],
    ];

    $response = $this->postJson('api/process-order', $orderData);
    $response->assertStatus(422);
});

it('sends a single email when any ingredient reaches below 50%', function () {
    $orderData = [
        'products' => [
            [
                'product_id' => 1, //['Beef'=>150,'Cheese'=>30,'Onion'=>20]
                'quantity' => 15,    
            ],
            [
                'product_id' => 2, //['Beef'=>100,'Cheese'=>60,'Onion'=>10]
                'quantity' => 21,   
            ],
        ],
    ];
    
    Event::fake([
        IngredientLowStock::class,
    ]);

    $this->postJson('api/process-order', $orderData);

    Event::assertDispatched(IngredientLowStock::class, 1);
    
});

it('updates the low_amount_notified_at timestamp after merchant is notified', function () {
    $orderData = [
        'products' => [
            [
                'product_id' => 1, //['Beef'=>150,'Cheese'=>30,'Onion'=>20]
                'quantity' => 15,    
            ],
            [
                'product_id' => 2, //['Beef'=>100,'Cheese'=>60,'Onion'=>10]
                'quantity' => 21,   
            ],
        ],
    ];

    $this->postJson('api/process-order', $orderData);
    sleep(2);
    expect(Ingredient::find(3)->low_amount_notified_at)->toBeNull();
});




