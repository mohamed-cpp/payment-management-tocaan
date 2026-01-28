<?php

namespace App\Services\PaymentGateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Exception;

class StripeGateway implements PaymentGatewayInterface
{

    public function processPayment(Order $order, array $paymentData): array
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));


            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => (int) ($order->total * 100), // cents
                'currency' => 'usd',
                'description' => 'Order #' . $order->id,
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);

            return [
                'success' => true,
                'status' => Payment::STATUS_SUCCESSFUL,
                'transaction_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'message' => 'Payment processed successfully',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'status' => Payment::STATUS_FAILED,
                'transaction_id' => null,
                'message' => 'Stripe payment failed: ' . $e->getMessage(),
            ];
        }
    }

    public function getName(): string
    {
        return 'stripe';
    }
}
