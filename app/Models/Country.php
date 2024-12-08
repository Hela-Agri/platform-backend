<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Country extends Model
{
    public $table = 'countries';

    public $fillable = [
        'id',
        'code',
        'country_code',
        'name',
        'phone_number_length',
        'administration_level',
        'administration_level_one_label',
        'administration_level_two_label'
    ];

    protected $casts = [
        'id' => 'string',
        'code' => 'string',
        'country_code' => 'string',
        'name' => 'string',
        'phone_number_length' => 'string',
        'administration_level_one_label' => 'string',
        'administration_level_two_label' => 'string'
    ];

    public static array $rules = [
        'code' => 'required|string|max:3',
        'country_code' => 'required|string|max:3',
        'name' => 'required|string|max:255',
        'phone_number_length' => 'required|string|max:255',
        'administration_level' => 'required',
        'administration_level_one_label' => 'required|string|max:255',
        'administration_level_two_label' => 'nullable|string|max:255',
        'deleted_at' => 'nullable',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function administrationLevelOnes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\AdministrationLevelOne::class, 'country_id');
    }

    public function administrationLevelTwos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\AdministrationLevelTwo::class, 'country_id');
    }
}
