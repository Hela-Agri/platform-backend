<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class AdministrationLevelOne extends Model
{
    public $table = 'administration_level_ones';

    public $fillable = [
        'id',
        'code',
        'name',
        'country_id'
    ];

    protected $casts = [
        'id' => 'string',
        'code' => 'string',
        'name' => 'string',
        'country_id' => 'string'
    ];

    public static array $rules = [
        'code' => 'required|string|max:5',
        'name' => 'required|string|max:255',
        'country_id' => 'required|string|max:36',
        'deleted_at' => 'nullable',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Country::class, 'country_id');
    }

    public function administrationLevelTwos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\AdministrationLevelTwo::class, 'administration_level_one_id');
    }
}
