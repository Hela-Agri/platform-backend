<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Loan extends Model
{
    use LogsActivity;
    public $table = 'loans';

    public $fillable = [
        'sub_total',
        'total',
        'balance',
        'code',
        'user_id',
        'farm_activity_id',
        'payment_status_id',
        'status_id',
        'wallet_transaction_id',
        'interest',
        'approval_date',
        'maturity_date',
        'processing_fee'
    ];

    protected $casts = [
        'sub_total' => 'double',
        'balance' => 'double',
        'processing_fee' => 'double',
        'total' => 'double',
        'code' => 'string',
        'user_id' => 'string',
        'payment_status_id' => 'string',
        'approval_date' => 'date',
        'maturity_date' => 'date',
        'farm_activity_id' => 'string',
        'wallet_transaction_id' => 'string',
        'status_id' => 'string',
        'interest' => 'double'
    ];

    public static array $rules = [
        'sub_total' => 'required',
        'total' => 'required',
        'code' => 'required',
        'user_id' => 'required',
        'approval_date' => 'required|date',
        'wallet_transaction_id' => 'required',
        'interest' => 'required',
        'processing_fee' => 'numeric',
    ];

    // Other properties and methods of your Farmer model

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('loan');
    }

    public function status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Status::class, 'status_id');
    }

    public function payment_status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Status::class, 'payment_status_id');
    }
    public function farmer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function walletTransaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'wallet_transaction_id')->with('wallet');
    }

    public function items()
    {
        return $this->hasMany(LoanItem::class, 'loan_id')->with('farm_activity_item');
    }

    public function loanPayments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoanPayment::class, 'loan_id')->with('payment');
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,        // The final model we want to access
            LoanPayment::class,    // The intermediate model we're going through
            'loan_id',             // Foreign key on LoanPayment table
            'id',                  // Local key on Payment table
            'id',                  // Local key on Loan table
            'payment_id'           // Foreign key on LoanPayment table
        );
    }


    public function farm_activity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FarmActivity::class, 'farm_activity_id')->with(['cohort', 'package', 'farm']);
    }
}
