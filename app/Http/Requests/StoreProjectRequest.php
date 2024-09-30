<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('store', Project::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string',],
            'description' => ['required', 'string'],
//            'members' => ['sometimes', 'array'],
//            'members.*' => [Rule::exists('users', 'id')]
        ];
    }
}
