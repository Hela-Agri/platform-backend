<?php

namespace App\Repositories;

use App\Models\Country;
use App\Repositories\BaseRepository;

class CountryRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'code',
        'country_code',
        'name',
        'phone_number_length',
        'administration_level',
        'administration_level_one_label',
        'administration_level_two_label'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Country::class;
    }
}
