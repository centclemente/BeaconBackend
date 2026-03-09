<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgressRequest extends FormRequest
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
            'system_id' => 'required|exists:systems,id',
            'categories' => 'required|array|min:1',
            'categories.*.categoryName' => 'required|string|exists:category,name',
            'categories.*.progress' => 'required|array|min:1',
            'categories.*.progress.*.description' => 'required|string',
            'categories.*.progress.*.raised_date' => 'required|date',
            'categories.*.progress.*.target_date' => 'nullable|date|after_or_equal:raised_date',
            'categories.*.progress.*.end_date' => 'nullable|date|after_or_equal:raised_date',
            'categories.*.progress.*.status' => 'nullable|in:done,pending,hold' ?? 'pending',
            'categories.*.progress.*.remarks' => 'nullable|string',
        ];
    }
}
