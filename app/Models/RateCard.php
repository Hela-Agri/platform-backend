<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class RateCard extends Model
{
    use LogsActivity;
    public $table = 'rate_cards';

    public $fillable = [
        'item_id',
        'item_type',
        'name',
        'amount',
        'effective_date'
    ];

    protected $casts = [
        'name' => 'string',
        'item_id' => 'string',
        'item_type' => 'string',
        'amount' => 'double',
        'effective_date' => 'datetime'
    ];

    public static array $rules = [
        'item_id' => 'required',
        'item_type' => 'required',
        'name' => 'required',
        'amount' => 'required',
        'effective_date' => 'required'
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'item_id')->with('unit');
    }

    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Service::class, 'item_id');
    }
     // Other properties and methods of your Farmer model

     public function getActivitylogOptions(): LogOptions
     {
         return LogOptions::defaults()
             ->logAll()
             ->useLogName('rate_card');
     }

}
