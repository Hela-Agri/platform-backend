<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Farm extends Model
{
    use SoftDeletes,LogsActivity;
    public $table = 'farms';

    public $fillable = [
        'id',
        'size',
        'ownership',
        'terrain',
        'unit_id',
        'latitude',
        'longitude',
        'location',
        'acres',
        'user_id'
    ];

    protected $casts = [
        'id' => 'string',
        'size' => 'string',
        'ownership' => 'string',
        'terrain' => 'string',
        'unit_id' => 'string',
        'latitude' => 'string',
        'acres' => 'string',
        'longitude' => 'string',
        'location' => 'string',
        'user_id' => 'string'
    ];

    public static array $rules = [
        'size' => 'required|string|max:255',
        'ownership' => 'required|string',
        'terrain' => 'required|string',
        'acres' => 'numeric',
        'unit_id' => 'required|string|max:255',
        'latitude' => 'nullable|numeric|max:255',
        'longitude' => 'nullable|numeric|max:255',
        'location' => 'required|string|max:255',
        'user_id' => 'required|string|max:36',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Unit::class, 'unit_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function farmActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\FarmActivity::class, 'farm_id');
    }
     // Other properties and methods of your Farmer model

     public function getActivitylogOptions(): LogOptions
     {
         return LogOptions::defaults()
             ->logAll()
             ->useLogName('farm');
     }
}
