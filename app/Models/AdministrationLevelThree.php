<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class AdministrationLevelThree extends Model
{
    public $table = 'administration_level_threes';

    public $fillable = [
        'id',
        'code',
        'name',
        'administration_level_two_id'
    ];

    protected $casts = [
        'id' => 'string',
        'code' => 'string',
        'name' => 'string',
        'administration_level_two_id' => 'string'
    ];

    public static array $rules = [
        'code' => 'required|string|max:5',
        'name' => 'required|string|max:255',
        'administration_level_two_id' => 'required|string|max:36',
        'deleted_at' => 'nullable',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function administrationLevelTwo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\AdministrationLevelTwo::class, 'administration_level_two_id');
    }
}
