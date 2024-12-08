<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
class SiteVisit extends Model
{
    public $table = 'site_visits';

    public $fillable = [
        'user_id',
        'farm_activity_id',
        'action_id',
        'longitude',
        'latitude',
        'remarks',
        'urgency'
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'farm_activity_id' => 'string',
        'action_id' => 'string',
        'longitude' => 'string',
        'latitude' => 'string',
        'remarks' => 'string',
        'urgency' => 'string'
    ];

    public static array $rules = [
        'user_id' => 'string|max:36',
        'farm_activity_id' => 'required|string|max:36',
        'action_id' => 'required|string|max:36',
        'longitude' => 'required|string|max:255',
        'latitude' => 'required|string|max:255',
        'remarks' => 'required|string|max:255',
        'urgency' => 'required|string',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function action(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Action::class, 'action_id');
    }

    public function farmActivity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\FarmActivity::class, 'farm_activity_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function uploads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Upload::class,'entity', 'id');
    }
}
