<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VtuProvider extends Model
{
    protected $fillable = ['name', 'api_key', 'api_token', 'base_url', 'is_active', 'success_rate'];

    public function plans()
    {
        return $this->hasMany(VtuPlan::class);
    }

    public function transactions()
    {
        return $this->hasMany(VtuTransaction::class);
    }
}