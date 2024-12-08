<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class FarmActivity extends Model
{
    use SoftDeletes;
    use LogsActivity;

    public $table = 'farm_activities';

    public $fillable = [
        'code',
        'cohort_id',
        'user_id',
        'farm_id',
        'loan_package_id',
        'wallet_id',
        'start_date',
        'end_date',
        'status_id',
        'invoice_number'
    ];

    protected $casts = [
        'code',
        'cohort_id' => 'string',
        'user_id' => 'string',
        'farm_id' => 'string',
        'loan_package_id' => 'string',
        'status_id' => 'string',
        'wallet_id' => 'string',
        'invoice_number' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public static array $rules = [
        'farm_activities' => 'required',
        'farm_activity_items' => 'required',
        //        'loan_package_id' => 'required',
    ];

    // Other properties and methods of your Farmer model

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('farm_activity');
    }

    public function activityItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FarmActivityItem::class, 'farm_activity_id')->with('rateCard')->orderBy('date', 'asc');
    }

    public function harvests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Harvest::class, 'farm_activity_id')->with('unit')->orderBy('harvest_date', 'asc');
    }
    public function siteVisits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SiteVisit::class, 'farm_activity_id')->with(['user', 'action', 'uploads'])->orderBy('created_at', 'desc');
    }

    public function farm(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Farm::class, 'farm_id')->with(['user', 'unit']);
    }

    public function loan(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Loan::class);
    }

    public function offtaker(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Wallet::class,
            'id', # foreign key on intermediary -- categories
            'id', # foreign key on target -- projects
            'wallet_id', # local key on this -- properties
            'user_id' # local key on intermediary -- categories
        );
    }
    public function center(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Center::class,
            Cohort::class,
            'id', // Foreign key on Cohort table
            'id', // Foreign key on Center table
            'cohort_id', // Local key on FarmActivity table
            'center_id' // Local key on Cohort table
        );
    }

    public function cohort(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cohort::class, 'cohort_id')->with('center');
    }

    public function farmer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LoanPackage::class, 'loan_package_id');
    }

    public function status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
