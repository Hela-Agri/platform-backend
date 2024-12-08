<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Deposit extends Model
{
    public $table = 'deposits';

    public $fillable = [
        'amount',
        'code',
        'wallet_transaction_id',
        'status_id',
        'allowed_amount',
        'balance',
        'date',
        'user_id'
    ];

    protected $casts = [
        'allowed_amount' => 'double',
        'balance' => 'double',
        'amount' => 'double',
        'date' => 'datetime',
        'code' => 'string',
        'user_id' => 'string',
        'status_id' => 'string',
    ];

    public static array $rules = [
        'amount' => 'required',
        'user_id' => 'required',
        'date'  => 'required'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id')->with('wallet');
    }

    public function status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Status::class, 'status_id');
    }

}
