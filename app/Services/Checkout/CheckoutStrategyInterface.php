<?php

namespace App\Services\Checkout;

use App\Models\Order;
use Illuminate\Http\Request;

interface CheckoutStrategyInterface
{
    /**
     * Validate the checkout request data.
     */
    public function validate(Request $request): array;

    /**
     * Process the order based on validated data and return the created Order.
     */
    public function process(array $validatedData): Order;
}
