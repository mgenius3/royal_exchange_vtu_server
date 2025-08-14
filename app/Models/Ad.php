<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'title', 'description', 'image_url', 'cloudinary_public_id', 'target_url', 'type',
        'is_active', 'start_date', 'end_date', 'priority'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];
}