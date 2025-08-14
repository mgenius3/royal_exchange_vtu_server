<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_name',
        'account_number',
        'ifsc_code',
        'swift_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}