<?php

namespace App\Repositories;

use App\Models\AdministrationLevelTwo;
use App\Repositories\BaseRepository;

class AdministrationLevelTwoRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'code',
        'name',
        'country_id',
        'administration_level_one_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return AdministrationLevelTwo::class;
    }
}
