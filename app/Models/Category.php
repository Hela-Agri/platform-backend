<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Category extends Model
{
    use LogsActivity;
    public $table = 'categories';

    public $fillable = [
        'id',
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
     // Other properties and methods of your Farmer model

     public function getActivitylogOptions(): LogOptions
     {
         return LogOptions::defaults()
             ->logAll()
             ->useLogName('category');
     }


}
