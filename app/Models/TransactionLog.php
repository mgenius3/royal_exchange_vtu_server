<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_type',
        'reference_id',
        'details',
        'success'
    ];

    protected $casts = [
        'details' => 'array', // Cast JSON to array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}