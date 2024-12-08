<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
class Farmer extends Model
{
    use SoftDeletes;
    use LogsActivity;
    public $table = 'users';

    public $fillable = [
        'id',
        'code',
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'username',
        'registration_number',
        'status_id',
        'role_id',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'administration_level_one_id',
        'administration_level_two_id',
        'administration_level_three_id',
        'country_id'
    ];

    protected $casts = [
        'id' => 'string',
        'code' => 'string',
        'first_name' => 'string',
        'middle_name' => 'string',
        'last_name' => 'string',
        'phone_number' => 'string',
        'username' => 'string',
        'registration_number' => 'string',
        'status_id' => 'string',
        'role_id' => 'string',
        'email' => 'string',
        'email_verified_at' => 'datetime',
        'password' => 'string',
        'remember_token' => 'string',
        'administration_level_one_id' => 'string',
        'administration_level_two_id' => 'string',
        'administration_level_three_id' => 'string',
        'country_id' => 'string'
    ];


    // Other properties and methods of your Farmer model

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('farmer');
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

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }

    public function status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Status::class, 'status_id');
    }

    public function deposits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Deposit::class, 'user_id');
    }

    public function farmActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\FarmActivity::class, 'user_id');
    }

    public function cohorts()
    {
        return $this->belongsToMany(Cohort::class, 'farm_activities','user_id', 'cohort_id');
    }


    public function farms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Farm::class, 'user_id');
    }

    public function kins(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Kin::class, 'user_id')->with([
            'relationship:id,name',
            'administrationLevelOne:id,name',
            'administrationLevelTwo:id,name',
            'administrationLevelThree:id,name',
        ]);
    }

    public function loans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Loan::class, 'user_id');
    }

    public function walletTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\WalletTransaction::class, 'user_id');
    }

    public function wallets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Wallet::class, 'user_id');
    }
}
