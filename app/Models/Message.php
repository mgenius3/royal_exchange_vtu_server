<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['chat_id', 'sender_type', 'content', 'is_read'];
}