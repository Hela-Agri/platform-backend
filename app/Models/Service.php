<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Service extends Model
{
    use LogsActivity;
    public $table = 'services';

    public $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string'
    ];

    public static array $rules = [
        'name' => 'required',
        'description' => 'required'
    ];

     // Other properties and methods of your Farmer model

     public function getActivitylogOptions(): LogOptions
     {
         return LogOptions::defaults()
             ->logAll()
             ->useLogName('service');
     }


}
