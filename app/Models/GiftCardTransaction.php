<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCardTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'gift_card_id',
        'country_id',
        'type',
        'status',
        'proof_file',
        'cloudinary_public_id',
        'tx_hash',
        'admin_notes',
        'payment_method',
        'gift_card_type', // Physical or ecode
        'ecode',
        'balance', // Total value of the gift card
        'fiat_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }
}
