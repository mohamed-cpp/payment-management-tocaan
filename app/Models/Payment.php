<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $fillable = [
        'order_id',
        'payment_method',
        'status',
        'amount',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // statuses
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_FAILED = 'failed';


    const METHOD_STRIPE = 'stripe';
    const METHOD_CASH = 'cash';


    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_SUCCESSFUL,
            self::STATUS_FAILED,
        ];
    }

    ##################################################
    /**
     * Get available payment methods.
     */
    public static function getMethods(): array
    {
        return [
            self::METHOD_STRIPE,
            self::METHOD_CASH,
        ];
    }

   ### relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
