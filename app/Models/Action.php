<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Action extends Model
{
    public $table = 'actions';

    public $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'description' => 'string'
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function siteVisits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\SiteVisit::class, 'action_id');
    }
}
