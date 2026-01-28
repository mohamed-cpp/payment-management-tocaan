<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentGateways\CashGateway;
use App\Services\PaymentGateways\StripeGateway;
use Exception;

class PaymentService
{
    //Add new gateways here
    protected array $gateways = [
        'stripe' => StripeGateway::class,
        'cash' => CashGateway::class,
    ];

    public function getGateway(string $gateway): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$gateway])) {
            throw new Exception("Payment '{$gateway}' is not supported.");
        }
        return new $this->gateways[$gateway]();
    }


    public function processPayment(Order $order, string $gatewayName, array $paymentData = []): array
    {
        // if paid order
        if (!$order->canBePaid()) {
            throw new Exception('Order must be in pending status to process payment.');
        }

        $gateway = $this->getGateway($gatewayName);
        $result = $gateway->processPayment($order, $paymentData);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => $gateway->getName(),
            'status' => $result['status'],
            'amount' => $order->total,
            'transaction_id' => $result['transaction_id'],
        ]);
        if (($result['status'] ?? null) === Payment::STATUS_SUCCESSFUL) {
            $order->status = Order::STATUS_CONFIRMED;
            $order->save();
        }

        return ['payment' => $payment, 'result' => $result];
    }

    public function getAvailableGateways(): array
    {
        return array_keys($this->gateways);
    }
}
