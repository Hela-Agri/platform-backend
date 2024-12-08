<?php

namespace App\Repositories;

use App\Models\LoanItem;
use App\Repositories\BaseRepository;

class LoanItemRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'item_id',
        'farm_activity_item_id',
        'balance',
        'status_id',
        'amount',
        'code',
        'loan_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return LoanItem::class;
    }
}
