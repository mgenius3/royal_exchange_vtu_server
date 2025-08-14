<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VtuPlan extends Model
{
    protected $fillable = [
        'vtu_provider_id', 'network', 'type', 'plan_code', 'description', 'price', 'commission', 'is_active'
    ];

    public function provider()
    {
        return $this->belongsTo(VtuProvider::class, 'vtu_provider_id');
    }

    public function transactions()
    {
        return $this->hasMany(VtuTransaction::class);
    }
}