<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FarmActivityItem extends Model
{
    use SoftDeletes;

    public $table = 'farm_activity_items';

    public $fillable = [
        'id',
        'farm_activity_id',
        'rate_card_id',
        'quantity',
        'total',
        'date'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'total' => 'decimal:2',
        'date' => 'datetime'
    ];

    public static array $rules = [
        'farm_activity_id' => 'required',
        'rate_card_id' => 'required',
        'quantity' => 'required',
        'total' => 'required',
        'date' => 'datetime'
    ];

    public function rateCard (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RateCard::class, 'rate_card_id')->with(['product', 'service']);
    }

    public function farm_activity (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FarmActivity::class, 'farm_activity_id')->with(['cohort:id,name', 'package']);
    }
}
