<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Wallet extends Model
{
    public $table = 'wallets';

    public $fillable = [
        'balance',
        'code',
        'user_id'
    ];

    protected $casts = [
        'balance' => 'double',
        'code' => 'string'
    ];

    public static array $rules = [
        'balance' => 'required',
        'code' => 'required',
        'user_id' => 'required'
    ];

    public function offTaker(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
