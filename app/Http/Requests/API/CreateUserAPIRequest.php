<?php

namespace App\Http\Requests\API;

use App\Models\User;
use InfyOm\Generator\Request\APIRequest;

class CreateUserAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return array(
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$this->user,
            'email' => 'required|string|max:255|unique:users,email,'.$this->user,
            'phone_number' => 'required|string|max:255|unique:users,phone_number,'.$this->user,
            'email_verified_at' => 'nullable',
            'password' => 'nullable|confirmed|string|max:255',
            'remember_token' => 'nullable|string|max:100',
            'deleted_at' => 'nullable',
            'created_at' => 'nullable',
            'updated_at' => 'nullable'
        );
    }
}
