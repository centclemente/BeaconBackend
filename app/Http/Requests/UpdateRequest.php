<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
    
        return [
            'first_name' => 'sometimes|string',
            'middle_name' => 'sometimes|nullable|string',
            'last_name' => 'sometimes|string',
            'suffix' => 'sometimes|nullable|string',
            'username' => 'sometimes|string|unique:users,username,'.$this->route('user'),
            'password' => 'sometimes|string', 
            'role_id' => 'sometimes|exists:role,id',
            'charging_id' => 'sometimes|nullable|exists:charging,id',
        ];
    }
}
