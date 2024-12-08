<?php

namespace App\Repositories;

use App\Models\FarmActivity;
use App\Repositories\BaseRepository;

class FarmActivityRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'cohort_id',
        'user_id',
        'farm_id',
        'loan_package_id',
        'wallet_id',
        'start_date',
        'end_date',
        'status_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return FarmActivity::class;
    }
}
