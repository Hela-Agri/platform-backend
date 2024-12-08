<?php

namespace App\Http\Requests\API;

use App\Models\Farmer;
use InfyOm\Generator\Request\APIRequest;

class UpdateFarmerAPIRequest extends APIRequest
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
            'code' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'status_id' => 'nullable|string|max:36',
            'role_id' => 'nullable|string|max:36',
            'email' => 'nullable|string|max:255',
            'email_verified_at' => 'nullable',
            'password' => 'nullable|string|max:255',
            'remember_token' => 'nullable|string|max:100',
            'administration_level_one_id' => 'nullable|string|max:36',
            'administration_level_two_id' => 'nullable|string|max:36',
            'administration_level_three_id' => 'nullable|string|max:36',
            'country_id' => 'nullable|string|max:36',
            'created_at' => 'nullable',
            'updated_at' => 'nullable',
            'deleted_at' => 'nullable'
        );
    }
}
