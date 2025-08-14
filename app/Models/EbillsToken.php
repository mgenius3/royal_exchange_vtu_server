<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class EbillsToken extends Model
{
    protected $fillable = ['token', 'expires_at'];
}