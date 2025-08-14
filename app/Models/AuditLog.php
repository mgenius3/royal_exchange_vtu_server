<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['admin_id', 'action', 'details'];

    protected $casts = ['details' => 'array'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}