<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppBaseModel extends Model
{

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if(!$model->id){
                $model->{$model->getKeyName()} = (string) Str::uuid();
                }
                $latest_record=DB::table($model->table)->latest()->first();
                if (in_array('code',$model->fillable) && empty($model->code)){
                    if($latest_record){
                        $code= (int)$latest_record->code;
                    }else{
                        $code= 0;
                    }

                    $model->code= str_pad($code+1, 4, '0', STR_PAD_LEFT);
                }
                if (in_array('invoice_number',$model->fillable) && empty($model->invoice_number)){
                    if($latest_record){
                        $invoice_number= (int)$latest_record->invoice_number;
                    }else{
                        $invoice_number= 0;
                    }
                    $model->invoice_number= str_pad($invoice_number+1, 5, '0', STR_PAD_LEFT);
                }
                if (in_array('status_id',$model->fillable) && empty($model->status_id))
                    $model->status_id=Status::where('code','ACTIVE')->first()->id;

                if (in_array('payment_status_id',$model->fillable) && empty($model->payment_status_id))
                    $model->payment_status_id=Status::where('code','PENDING')->first()->id;
        });

        Pivot::creating(function($pivot) {
            $pivot->id = (string) Str::uuid();

        });

        static::retrieved(function ($model) {

            $exclude_uppercase=['modules'];

            if(Schema::hasColumn($model->table, 'first_name')){

                if (is_string($model->first_name)){
                    $model->first_name =trim(strtoupper($model->first_name));
                }else{
                    $model->first_name ="";
                }
            }

            if(Schema::hasColumn($model->table, 'middle_name')){
                if (is_string($model->middle_name)){
                    $model->middle_name =trim(strtoupper($model->middle_name));
                }else{
                    $model->middle_name ="";
                }
            }

            if(Schema::hasColumn($model->table, 'last_name')){
                if (is_string($model->last_name)){
                    $model->last_name =trim($model->last_name);
                }else{
                    $model->last_name ="";
                }
            }

            if(!in_array('modules',$exclude_uppercase) && $model->table!==Schema::hasColumn($model->table, 'name')){
                if (is_string($model->name)){
                    $model->name =trim($model->name);
                }else{
                    $model->name ="";
                }
            }

            if(Schema::hasColumn($model->table, 'email')){
                if (is_string($model->email)){
                    $model->email =trim($model->email);
                }else{
                    $model->email="";
                }
            }

            if(Schema::hasColumn($model->table, 'item')){
                if (is_string($model->item)){
                    $model->item =trim($model->item);
                }else{
                    $model->item ="";
                }
            }

        });
    }
}
