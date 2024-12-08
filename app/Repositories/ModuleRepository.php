<?php

namespace App\Repositories;

use App\Models\Module;
use App\Repositories\BaseRepository;

class ModuleRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id',
        'name',
        'has_approve',
        'deactivate',
        'activate',
        'has_download',
        'upload',
        'has_print'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Module::class;
    }
}
