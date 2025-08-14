<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VtuOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'request_id',
        'user_id',
        'product_name',
        'status',
        'amount',
        'amount_charged',
        'meta_data',
        'receipt_data', // Add this
        'date_created',
        'date_updated',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'receipt_data' => 'array', // Add this
        'date_created' => 'datetime',
        'date_updated' => 'datetime',
    ];
}