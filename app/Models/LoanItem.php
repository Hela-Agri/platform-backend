<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class LoanItem extends Model
{
    public $table = 'loan_items';

    public $fillable = [
        'item_id',
        'amount',
        'balance',
        'farm_activity_item_id',
        'status_id',
        'code',
        'loan_id'
    ];

    protected $casts = [
        'item_id' => 'string',
        'farm_activity_item_id' => 'string',
        'status_id' => 'string',
        'amount' => 'double',
        'balance' => 'double',
        'code' => 'string'
    ];

    public static array $rules = [
        'item_id' => 'required',
        'amount' => 'required',
        'code' => 'required'
    ];

    public function farm_activity_item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FarmActivityItem::class, 'farm_activity_item_id')->with(['rateCard', 'farm_activity']);
    }

}
