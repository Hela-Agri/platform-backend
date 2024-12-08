<?php

namespace App\Models;

use App\Models\AppBaseModel as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMode extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'description',
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'description' => 'string'
    ];
}
