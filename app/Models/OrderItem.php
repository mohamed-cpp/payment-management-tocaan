<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_name',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    #attributes
    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    ### relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}

