<?php

namespace App\Repositories;

use App\Models\FarmActivityItem;
use App\Repositories\BaseRepository;

class FarmActivityItemRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'farm_activity_id',
        'rate_card_id',
        'quantity',
        'total',
        'date'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return FarmActivityItem::class;
    }
}
