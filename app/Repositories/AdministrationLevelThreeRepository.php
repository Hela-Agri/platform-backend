<?php

namespace App\Repositories;

use App\Models\AdministrationLevelThree;
use App\Repositories\BaseRepository;

class AdministrationLevelThreeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'code',
        'name',
        'administration_level_two_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return AdministrationLevelThree::class;
    }
}
