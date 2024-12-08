<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Upload
 * @package App\Models
 * @version May 30, 2023, 4:37 am EAT
 *
 */
class Upload extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'uploads';


    protected $dates = ['deleted_at'];

    public $fillable = [
        'entity',
        'entity_type',
        'user_id',
        'file_name',
        'path',
        'ext',
        'size',
        'type',
        'original_type',
        'organization_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
            'entity' => 'string',
            'entity_type' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'entity' => 'required',
        'entity_type' => 'required'
    ];
}
