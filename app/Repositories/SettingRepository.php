<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\BaseRepository;

class SettingRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'email',
        'phone',
        'address',
        'slogan',
        'kra_pin',
        'invoice_note',
        'payment_note'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Setting::class;
    }
}
