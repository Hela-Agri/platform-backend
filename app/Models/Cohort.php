<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Cohort extends Model
{
    use SoftDeletes,LogsActivity;
    public $table = 'cohorts';

    public $fillable = [
        'id',
        'name',
        'code',
        'description',
        'duration',
        'center_id'
    ];

    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'code' => 'string',
        'description' => 'string',
        'duration' => 'string',
        'center_id' => 'string',
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'code' => 'string|max:255',
        'description' => 'required|string|max:255',
        'duration' => 'required|numeric|max:255',
        'center_id' => 'required|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];
    // Other properties and methods of your Farmer model

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('center');
    }

    public function farmActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\FarmActivity::class, 'cohort_id');
    }

    public function center(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Center::class, 'center_id');
    }
}
