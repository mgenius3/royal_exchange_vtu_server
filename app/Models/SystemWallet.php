<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemWallet extends Model
{
    protected $fillable = ['crypto_currency_id', 'balance', 'address'];

    public function cryptoCurrency()
    {
        return $this->belongsTo(CryptoCurrency::class);
    }
}