<?php

namespace App\Interfaces;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function processPayment(Order $order, array $paymentData): array;

    public function getName(): string;
}
