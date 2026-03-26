<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
         $roleId = $this->route('id') ?? $this->route('role');
         $isUpdated = $this->isMethod('PUT') || $this->isMethod('PATCH');
        return [
            'name' => 'required|string|max:255|unique:role,name,' . ($isUpdated ? $roleId : ''),
            'access_permissions' => ($isUpdated ? 'sometimes|' : 'required|') . 'array',
            'access_permissions.*' => 'string|distinct',
        ];
    }
}
