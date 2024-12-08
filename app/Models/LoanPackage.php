<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class LoanPackage extends Model
{
    public $table = 'loan_packages';

    public $fillable = [
        'interest_rate',
        'processing_fee',
        'processing_fee_desc',
        'duration',
        'code',
        'name',
        'interest_rate',
        'rate_type'
    ];

    protected $casts = [
        'rate_type' => 'string',
        'interest_rate' => 'string',
        'processing_fee_desc'=> 'string',
        'processing_fee' => 'double',
        'duration' => 'string',
        'code' => 'string',
        'name' => 'string'
    ];

    public static array $rules = [
        'interest_rate' => 'required|numeric',
        'processing_fee_desc' => 'nullable|string',
        'rate_type' => 'required',
        'processing_fee'=> 'required',
        'duration' => 'required',
        'name' => 'required'
    ];


}
