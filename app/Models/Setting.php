<?php

namespace App\Models;

use \App\Models\AppBaseModel as Model;

class Setting extends Model
{
    public $table = 'settings';

    public $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'address',
        'slogan',
        'kra_pin',
        'invoice_note',
        'payment_note'
    ];

    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'address' => 'string',
        'slogan' => 'string',
        'kra_pin' => 'string',
        'invoice_note' => 'string',
        'payment_note' => 'string'
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'phone' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'slogan' => 'required|string|max:255',
        'kra_pin' => 'required|string|max:255',
        'invoice_note' => 'required|string|max:1000',
        'payment_note' => 'required|string|max:1000',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function uploads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Upload::class,'entity', 'id')->where('entity_type','logo_file');
    }
    
}
