<?php

namespace App\Repositories;

use App\Models\LoanPackage;
use App\Repositories\BaseRepository;

class LoanPackageRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'interest_rate',
        'duration',
        'code',
        'name',
        'interest_rate'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return LoanPackage::class;
    }
}
