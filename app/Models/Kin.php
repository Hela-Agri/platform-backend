<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Kin extends Model
{
    use SoftDeletes,LogsActivity;
    public $table = 'kins';

    public $fillable = [
        'id',
        'code',
        'name',
        'phone_number',
        'registration_number',
        'postal_address',
        'administration_level_one_id',
        'administration_level_two_id',
        'administration_level_three_id',
        'country_id',
        'relationship_id',
        'user_id'
    ];

    protected $casts = [
        'id' => 'string',
        'code' => 'string',
        'name' => 'string',
        'phone_number' => 'string',
        'registration_number' => 'string',
        'postal_address' => 'string',
        'administration_level_one_id' => 'string',
        'administration_level_two_id' => 'string',
        'administration_level_three_id' => 'string',
        'country_id' => 'string',
        'relationship_id' => 'string',
        'user_id' => 'string'
    ];

    public static array $rules = [
        'code' => 'nullable|string|max:255',
        'name' => 'required|string|max:255',
        'phone_number' => 'required|string|max:255',
        'registration_number' => 'nullable|string|max:255',
        'postal_address' => 'required|string|max:255',
        'administration_level_one_id' => 'nullable|string|max:36',
        'administration_level_two_id' => 'nullable|string|max:36',
        'administration_level_three_id' => 'nullable|string|max:36',
        'country_id' => 'nullable|string|max:36',
        'relationship_id' => 'nullable|string|max:36',
        'user_id' => 'nullable|string|max:36',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

     // Other properties and methods of your Farmer model

     public function getActivitylogOptions(): LogOptions
     {
         return LogOptions::defaults()
             ->logAll()
             ->useLogName('kin');
     }
    public function administrationLevelOne(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\AdministrationLevelOne::class, 'administration_level_one_id');
    }

    public function administrationLevelThree(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\AdministrationLevelThree::class, 'administration_level_three_id');
    }

    public function administrationLevelTwo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\AdministrationLevelTwo::class, 'administration_level_two_id');
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Country::class, 'country_id');
    }

    public function relationship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Relationship::class, 'relationship_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
