<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisplaySystemRequest extends FormRequest
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
            "status" => ["required", "in:active,inactive"],
            "scope" => ["required", "in:global,per_team"],
            "team_id" => ["required_if:scope,per_team", "exists:teams,id"]
        ];
    }
}
 