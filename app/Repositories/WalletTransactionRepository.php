<?php

namespace App\Repositories;

use App\Models\WalletTransaction;
use App\Repositories\BaseRepository;

class WalletTransactionRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'amount',
        'code',
        'wallet_id',
        'wallet_id',
        'type',
        'user_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return WalletTransaction::class;
    }
}
