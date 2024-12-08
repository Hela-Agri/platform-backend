<?php

namespace App\Repositories;

use App\Models\Cohort;
use App\Repositories\BaseRepository;

class CohortRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'code',
        'description',
        'duration'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Cohort::class;
    }
}
