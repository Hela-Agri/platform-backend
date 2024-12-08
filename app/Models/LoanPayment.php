<?php

namespace App\Models;

use App\Models\AppBaseModel as Model;

class LoanPayment extends Model
{
    public $table = 'loan_payments';

    public $fillable = [
        'payment_id',
        'loan_id',
        'amount',
        'balance'
    ];

    protected $casts = [
        'balance' => 'double',
        'amount' => 'double',
        'payment_id' => 'string',
        'loan_id' => 'string'
    ];

    public static array $rules = [
        'payment_id' => 'required',
        'loan_id' => 'required',
        'balance' => 'required'
    ];

    public function loan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    public function payment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

}
