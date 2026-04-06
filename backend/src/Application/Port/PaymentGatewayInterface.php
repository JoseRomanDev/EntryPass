<?php

namespace App\Application\Port;

interface PaymentGatewayInterface
{
    /**
     * @param float $amount The total amount to be charged.
     * @return bool True if payment is successful.
     * @throws \Exception If payment fails, it will throw an exception to abort the purchase.
     */
    public function processPayment(float $amount): bool;
}
