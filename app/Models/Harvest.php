<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Harvest extends Model
{
    use SoftDeletes;
    use LogsActivity;
    public $table = 'harvests';

    public $fillable = [
        'id',
        'weight',
        'unit_id',
        'farm_activity_id',
        "harvest_date",
        'user_id'
    ];

    protected $casts = [
        'id' => 'string',
        'weight' => 'float',
        'unit_id' => 'string',
        "harvest_date" => 'datetime',
        'farm_activity_id' => 'string',
        'user_id' => 'string'
    ];

    public static array $rules = [
        'weight' => 'required|numeric',
        'unit_id' => 'required|string|max:255',
        'harvest_date'=> 'required|date',
        'farm_activity_id' => 'required|string|max:255',
        'user_id' => 'nullable|string|max:36',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

     // Other properties and methods of your Farmer model

     public function getActivitylogOptions(): LogOptions
     {
         return LogOptions::defaults()
             ->logAll()
             ->useLogName('yield');
     }

    public function farmActivity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\FarmActivity::class, 'farm_activity_id')->with(['cohort', 'package', 'farm']);
    }

    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Unit::class, 'unit_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
