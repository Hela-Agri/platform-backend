<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Center extends Model
{
    use LogsActivity;
    public $table = 'centers';

    public $fillable = [
        'id',
        'name',
        'code',
        'description'
    ];

    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'code' => 'string',
        'description' => 'string'
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'code' => 'string|max:255',
        'description' => 'required|string|max:255',
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

    public function cohorts()
    {
        return $this->hasMany(Cohort::class);
    }

    public function farmActivities()
    {
        return $this->hasManyThrough(FarmActivity::class, Cohort::class);
    }


}
