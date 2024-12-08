<?php

namespace App\Repositories;

use App\Models\Kin;
use App\Repositories\BaseRepository;

class KinRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'code',
        'name',
        'phone_number',
        'registration_number',
        'postal_address',
        'administration_level_one_id',
        'administration_level_two_id',
        'administration_level_three_id',
        'country_id',
        'relationship_id',
        'user_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Kin::class;
    }
}
