<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Payment::whereHas('order', function ($q) {
            $q->where('user_id', auth('api')->id());
        })->with('order');

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($payments);
    }

    public function show(Payment $payment): JsonResponse
    {
        if (! auth()->user()->can('view', $payment)) {
            return response()->json(['message' => 'Payment not found',], 404);
        }

        $payment->load('order');

        return response()->json($payment);
    }

    public function process(ProcessPaymentRequest $request): JsonResponse
    {
        $order = Order::findOrFail($request->order_id);

        if (! auth()->user()->can('view', $order)) {
            return response()->json(['message' => 'Order not found',], 404);
        }

        try {
            $result = $this->paymentService->processPayment($order, $request->payment_method, $request->all());
            $payment = $result['payment'];

            $payment->load('order');

            return response()->json([
                'message' => 'Payment processed successfully',
                'payment' => $payment,
                'client_secret' => $result['result']['client_secret'],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function gateways(): JsonResponse
    {
        return response()->json([
            'gateways' => $this->paymentService->getAvailableGateways(),
        ]);
    }
}
