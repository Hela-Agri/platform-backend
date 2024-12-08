<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Status extends Model
{
    public $table = 'statuses';

    public $fillable = [
        'code',
        'name'
    ];

    protected $casts = [
        'id' => 'string',
        'code' => 'string',
        'name' => 'string'
    ];

    public static array $rules = [
        'code' => 'string|max:10',
        'name' => 'required|string|max:30'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'status_id');
    }
}
