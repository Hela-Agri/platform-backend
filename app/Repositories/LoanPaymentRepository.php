<?php

namespace App\Repositories;

use App\Models\LoanPayment;
use App\Repositories\BaseRepository;

class LoanPaymentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'payment_id',
        'loan_id',
        'balance'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return LoanPayment::class;
    }
}
