<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailVerification extends Model
{
    use HasFactory;

    protected $table = 'email_verifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'code',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Check if the verification code has expired
     */
    public function hasExpired()
    {
        return $this->expires_at < Carbon::now();
    }

    /**
     * Check if the verification code is still valid
     */
    public function isValid()
    {
        return !$this->hasExpired();
    }

    /**
     * Scope to get only valid (non-expired) verifications
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope to get expired verifications
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', Carbon::now());
    }
}