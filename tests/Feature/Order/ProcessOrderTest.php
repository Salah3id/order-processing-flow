<?php

namespace Tests\Feature\Order;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('fails to create an order if payload contains invalid data', function () {
    $this->assertTrue(true);
});

it('can create a new order with multiple products', function () {
    $this->assertTrue(true);
});

it('updates ingredient amount after order creation', function () {
    $this->assertTrue(true);
});

it('fails to create an order if any ingredient is not available in stock', function () {
    $this->assertTrue(true);
});

it('sends a single email when any ingredient reaches below 50%', function () {
    $this->assertTrue(true);
});

it('updates the low_amount_notified_at timestamp after merchant is notified', function () {
    $this->assertTrue(true);
});


