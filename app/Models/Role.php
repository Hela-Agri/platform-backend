<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Role extends Model
{
    public $table = 'roles';

    public $fillable = [
        'name',
        'description',
        'code'
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'code' => 'string'
    ];




    public function permissions(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {

        return $this->hasManyThrough(
            \App\Models\Permission::class,
            \App\Models\RolePermission::class,
            'role_id', // Foreign key on the RolePermission table...
            'id', // Foreign key on the Permission table...
            'id', // Local key on the Permission table...
            'permission_id' // Local key on the RolePermission table...
        )->orderBy('name');


    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function rolePermissions()
    {
        return $this->belongsToMany(\App\Models\Permission::class,'role_permissions')->withTimestamps()->orderBy('name','asc');
    }


}
