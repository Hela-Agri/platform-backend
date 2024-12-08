<?php

namespace App\Repositories;

use App\Models\Farm;
use App\Repositories\BaseRepository;

class FarmRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'size',
        'ownership',
        'terrain',
        'unit_id',
        'latitude',
        'longitude',
        'location',
        'user_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Farm::class;
    }
}
