<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class WalletTransaction extends Model
{
    public $table = 'wallet_transactions';

    public $fillable = [
        'amount',
        'code',
        'wallet_id',
        'type',
        'user_id'
    ];

    protected $casts = [
        'amount' => 'double',
        'code' => 'string',
        'wallet_id' => 'string',
        'type' => 'string',
        'user_id' => 'string'
    ];

    public static array $rules = [
        'amount' => 'required',
        'code' => 'required',
        'wallet_id' => 'required',
        'type' => 'required',
        'user_id' => 'required'
    ];

    public function wallet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Wallet::class, 'wallet_id')->with('offTaker');
    }

    public function farmer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
