<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $result = $this->user()->can('store', Project::class);
        return $result;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string'
        ];
    }
}
