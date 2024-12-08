<?php

namespace App\Repositories;

use App\Models\Harvest;
use App\Repositories\BaseRepository;

class HarvestRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'weight',
        'unit_id',
        'farm_activity_id',
        'user_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Harvest::class;
    }
}
