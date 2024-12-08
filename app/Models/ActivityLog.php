<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $table = 'activity_log';

    public $fillable = [

    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }


}
