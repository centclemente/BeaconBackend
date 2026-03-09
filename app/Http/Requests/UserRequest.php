<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'suffix' => 'nullable|string',
            'username' => 'required|string|unique:users,username,',
            'password' => 'nullable|string',
            'role_id' => 'required|exists:role,id',
            'charging_id' => 'required|exists:charging,id',
            'team_id' => 'required|exists:teams,id',
        ];
    }
}
