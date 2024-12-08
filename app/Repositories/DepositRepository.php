<?php

namespace App\Repositories;

use App\Models\Deposit;
use App\Repositories\BaseRepository;

class DepositRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'amount',
        'code',
        'wallet_transaction_id',
        'status',
        'user_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Deposit::class;
    }
}
