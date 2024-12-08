<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class RolePermission extends Model
{
    public $table = 'role_permissions';

    public $fillable = [
        'id',
        'role_id',
        'permission_id'
    ];

    protected $casts = [
        'id' => 'string',
        'role_id' => 'string',
        'permission_id' => 'string'
    ];

    public static array $rules = [
        'role_id' => 'required|string|max:255',
        'permission_id' => 'required|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function permission(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Permission::class, 'permission_id');
    }

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }
}
