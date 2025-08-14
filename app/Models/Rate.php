<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = ['gift_card_id', 'currency', 'buy_rate', 'sell_rate', 'updated_by'];

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }
}