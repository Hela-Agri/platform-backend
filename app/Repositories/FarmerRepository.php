<?php

namespace App\Repositories;

use App\Models\Farmer;
use App\Repositories\BaseRepository;

class FarmerRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'code',
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'username',
        'registration_number',
        'status_id',
        'role_id',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'administration_level_one_id',
        'administration_level_two_id',
        'administration_level_three_id',
        'country_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Farmer::class;
    }
}
