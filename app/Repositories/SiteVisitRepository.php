<?php

namespace App\Repositories;

use App\Models\SiteVisit;
use App\Repositories\BaseRepository;

class SiteVisitRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'user_id',
        'farm_activity_id',
        'action_id',
        'longitude',
        'latitude',
        'remarks',
        'urgency'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return SiteVisit::class;
    }
}
