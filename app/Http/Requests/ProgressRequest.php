<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'system_id' => 'required|exists:systems,id',
            'categories' => 'required|array|min:1',
            'categories.*.categoryName' => 'required|string|exists:category,name',
            'categories.*.progress' => 'required|array|min:1',
            'categories.*.progress.*.description' => 'required|string',
            'categories.*.progress.*.raised_date' => 'required|date',
            'categories.*.progress.*.target_date' => 'nullable|date',
            'categories.*.progress.*.end_date' => 'nullable|date',
            'categories.*.progress.*.status' => 'nullable|in:done,pending,hold',
            'categories.*.progress.*.remarks' => 'nullable|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->categories ?? [] as $catIndex => $category) {
                foreach ($category['progress'] ?? [] as $progIndex => $progress) {
                    $raised = $progress['raised_date'] ?? null;
                    $target = $progress['target_date'] ?? null;
                    $end = $progress['end_date'] ?? null;
                    
                    if ($target && $raised && $target < $raised) {
                        $validator->errors()->add(
                            "categories.{$catIndex}.progress.{$progIndex}.target_date",
                            'Target date must be after or equal to raised date.'
                        );
                    }
                    
                    if ($end && $raised && $end < $raised) {
                        $validator->errors()->add(
                            "categories.{$catIndex}.progress.{$progIndex}.end_date",
                            'End date must be on or after raised date.'
                        );
                    }
                }
            }
        });
    }
}