<?php

namespace App\Services\PaymentGateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;

class CashGateway implements PaymentGatewayInterface
{

    public function processPayment(Order $order, array $paymentData): array
    {
        $transactionId = 'CASH-' . time() . '-' . $order->id;
        return [
            'success' => true,
            'status' => Payment::STATUS_SUCCESSFUL,
            'transaction_id' => $transactionId,
            'client_secret' => null,
            'message' => 'Payment processed successfully',
        ];
    }

    public function getName(): string
    {
        return 'cash';
    }
}
