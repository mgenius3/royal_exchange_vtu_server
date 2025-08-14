<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CryptoTransaction extends Model
{
    protected $fillable = [
        'user_id', 'crypto_currency_id', 'type', 'amount', 'fiat_amount', 'status',
        'payment_method', 'wallet_address', 'confirmations', 'tx_hash', 'admin_notes',
        'proof_file' , 'cloudinary_public_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cryptoCurrency()
    {
        return $this->belongsTo(CryptoCurrency::class);
    }
}