<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Repositories\BaseRepository;

class ActivityLogRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return ActivityLog::class;
    }
}
