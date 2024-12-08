<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Permission extends Model
{
    public $table = 'permissions';

    public $fillable = [
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
        'code' => 'required|string|max:255',
        'description' => 'required|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function rolePermissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\RolePermission::class, 'permission_id');
    }
}
