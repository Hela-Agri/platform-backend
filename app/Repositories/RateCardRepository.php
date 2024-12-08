<?php

namespace App\Repositories;

use App\Models\RateCard;
use App\Repositories\BaseRepository;

class RateCardRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'amount',
        'item_id',
        'item_type',
        'effective_date'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return RateCard::class;
    }
}
