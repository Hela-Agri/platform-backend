<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

class UniqueField implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    protected $id;
    protected $attribute;
    protected $table;
    protected $message;

    public function __construct($id, $_message,$tbl)
    {
       $this->id = $id;
       $this->table = $tbl;
       $this->message = $_message;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute=$attribute;


        $entity=\DB::table($this->table)
            ->where($attribute, $value)->whereNull('deleted_at')->latest()->first();


        if ($entity) {
                 //check if its the same $entity being updated
                if($entity->id!=$this->id){
                    return false;
                }else{
                    return true;
                }


        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
