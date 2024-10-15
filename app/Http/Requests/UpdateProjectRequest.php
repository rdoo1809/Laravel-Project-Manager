<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');
        return $this->user()->can('update', $project);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string'
        ];
    }
}
