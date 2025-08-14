<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VtuTransaction extends Model
{
    protected $fillable = [
        'user_id', 'vtu_plan_id', 'vtu_provider_id', 'phone_number', 'account_number', 'amount',
        'status', 'transaction_id', 'response_message', 'is_refunded', 'admin_notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(VtuPlan::class, 'vtu_plan_id');
    }

    public function provider()
    {
        return $this->belongsTo(VtuProvider::class, 'vtu_provider_id');
    }
}