<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitWallet extends Model
{
    protected $table = 'profit_wallets'; // Specify the table name
    protected $fillable = ['type', 'balance'];

    // Method to update balance
    public function updateBalance($amount)
    {
        $this->balance += $amount;
        $this->save();
    }
}