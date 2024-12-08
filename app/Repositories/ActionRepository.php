<?php

namespace App\Repositories;

use App\Models\Action;
use App\Repositories\BaseRepository;

class ActionRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'description'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Action::class;
    }
}
