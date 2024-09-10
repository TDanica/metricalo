<?php

namespace App\Service\PaymentStrategy;

interface PaymentStrategy
{
    public function charge(float $amount, string $currency, string $cardNumber, ?int $expYear = null, ?int $expMonth = null, ?string $cvv = null): array;
}
