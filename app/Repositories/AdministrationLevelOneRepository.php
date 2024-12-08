<?php

namespace App\Repositories;

use App\Models\AdministrationLevelOne;
use App\Repositories\BaseRepository;

class AdministrationLevelOneRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'code',
        'name',
        'country_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return AdministrationLevelOne::class;
    }
}
