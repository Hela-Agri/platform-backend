<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Relationship extends Model
{
    public $table = 'relationships';

    public $fillable = [
        'id',
        'name'
    ];

    protected $casts = [
        'id' => 'string',
        'name' => 'string'
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];


}
