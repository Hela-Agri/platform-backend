<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Unit extends Model
{
    public $table = 'units';

    public $fillable = [
        'id',
        'name',
        'ratio',
        'classification'
    ];

    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'ratio' => 'string',
        'classification' => 'string'
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'ratio' => 'string',
        'classification' => 'required|string|in:product,farm',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function farms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Farm::class, 'unit_id');
    }
}
