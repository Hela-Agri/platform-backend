<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Product extends Model
{
    use LogsActivity;
    public $table = 'products';

    public $fillable = [
        'name',
        'description',
        'category_id',
        'unit_id',
        'pack_size'
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'category_id' => 'string',
        'unit_id' => 'string',
        'pack_size' => 'double'
    ];

    public static array $rules = [
        'name' => 'required',
        'description' => 'required',
        'category_id' => 'required|string',
        'unit_id' => 'required|string',
        'pack_size' => 'required|numeric'
    ];
     // Other properties and methods of your Farmer model

     public function getActivitylogOptions(): LogOptions
     {
         return LogOptions::defaults()
             ->logAll()
             ->useLogName('product');
     }

    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

}
