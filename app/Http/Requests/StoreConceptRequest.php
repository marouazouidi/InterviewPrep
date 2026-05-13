<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConceptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'explanation' => ['required', 'string'],
            'difficulty' => ['required', 'in:junior,mid,senior'],
        ];
    }
}
