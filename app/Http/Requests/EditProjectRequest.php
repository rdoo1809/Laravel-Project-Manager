<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class EditProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Project::class);
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
