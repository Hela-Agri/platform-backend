<?php

namespace App\Repositories;

use App\Models\Loan;
use App\Repositories\BaseRepository;

class LoanRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'sub_total',
        'total',
        'balance',
        'code',
        'user_id',
        'payment_status_id',
        'wallet_transaction_id',
        'interest'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Loan::class;
    }
}
