<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Module extends Model
{
    public $table = 'modules';

    public $fillable = [
        'id',
        'name',
        'has_approve',
        'deactivate',
        'activate',
        'has_download',
        'upload',
        'has_print'
    ];

    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'has_approve' => 'boolean',
        'deactivate' => 'boolean',
        'activate' => 'boolean',
        'has_download' => 'boolean',
        'upload' => 'boolean',
        'has_print' => 'boolean'
    ];

    public static array $rules = [
        'name' => 'required|string|max:65535',
        'has_approve' => 'required|boolean',
        'deactivate' => 'nullable|boolean',
        'activate' => 'nullable|boolean',
        'has_download' => 'nullable|boolean',
        'upload' => 'nullable|boolean',
        'has_print' => 'nullable|boolean',
        'deleted_at' => 'nullable',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];


}
