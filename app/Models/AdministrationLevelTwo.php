<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class AdministrationLevelTwo extends Model
{
    public $table = 'administration_level_twos';

    public $fillable = [
        'id',
        'code',
        'name',
        'country_id',
        'administration_level_one_id'
    ];

    protected $casts = [
        'id' => 'string',
        'code' => 'string',
        'name' => 'string',
        'country_id' => 'string',
        'administration_level_one_id' => 'string'
    ];

    public static array $rules = [
        'code' => 'required|string|max:12',
        'name' => 'required|string|max:255',
        'country_id' => 'required|string|max:36',
        'administration_level_one_id' => 'required|string|max:36',
        'deleted_at' => 'nullable',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function administrationLevelOne(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\AdministrationLevelOne::class, 'administration_level_one_id');
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Country::class, 'country_id');
    }
}
