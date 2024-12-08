<?php

namespace App\Models;

use App\Models\AppBaseModel as Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Payment extends Model
{
    use LogsActivity;
    public $table = 'payments';

    public $fillable = [
        'paid_amount',
        'transaction_reference',
        'balance',
        'merchant_request_id',
        'checkout_request_id',
        'transaction_date',
        'note',
        'payment_mode_id',
        'user_id'
    ];

    protected $casts = [
        'paid_amount' => 'double',
        'balance' => 'double',
        'transaction_reference' => 'string',
        'checkout_request_id' => 'string',
        'transaction_date' => 'date',
        'payment_mode_id' => 'string'
    ];

    public static array $rules = [
        'paid_amount' => 'required',
        'transaction_reference' => 'required',
        'transaction_date' => 'required',
        'payment_mode_id' => 'required',
    ];

    // Other properties and methods of your Farmer model

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('payment');
    }
    public function payment_mode(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\PaymentMode::class);
    }

    public function offTaker(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function loanPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\LoanPayment::class)->with('loan');
    }

    public function loans(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Loan::class,        // The final model we want to access
            LoanPayment::class,    // The intermediate model we're going through
            'payment_id',             // Foreign key on LoanPayment table
            'id',                  // Local key on Payment table
            'id',                  // Local key on Loan table
            'loan_id'           // Foreign key on LoanPayment table
        )->with(['status', 'farmer']);
    }
}
