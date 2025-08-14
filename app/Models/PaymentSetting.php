<?php

// app/Models/PaymentSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'gateway',
        'public_key',
        'secret_key',
        'encryption_key',
        'webhook_secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
