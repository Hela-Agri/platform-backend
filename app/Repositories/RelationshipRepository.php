<?php

namespace App\Repositories;

use App\Models\Relationship;
use App\Repositories\BaseRepository;

class RelationshipRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Relationship::class;
    }
}
