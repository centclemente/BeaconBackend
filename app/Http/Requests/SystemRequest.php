<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SystemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
         return [
           'system_name' => [
            'required',
            'string',
            'max:255',
        ],  
            'team_id' => 'required|array',
            'team_id.*' => [
            'required',
            Rule::exists('teams', 'id')->whereNull('deleted_at')
            ],
        ];
    }
}
