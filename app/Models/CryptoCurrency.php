<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoCurrency extends Model
{
    protected $fillable = ['name', 'symbol', 'network', 'buy_rate', 'sell_rate', 'is_enabled', 'image','cloudinary_public_id', 'wallet_address', 'current_price',];

    public function transactions()
    {
        return $this->hasMany(CryptoTransaction::class);
    }

    public function wallet()
    {
        return $this->hasOne(SystemWallet::class);
    }
}