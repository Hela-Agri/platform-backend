<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Repositories\BaseRepository;

class WalletRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'balance',
        'code',
        'user_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Wallet::class;
    }
}
