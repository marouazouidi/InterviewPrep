<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'color' => ['required', 'string', 'in:bg-blue-500,bg-green-500,bg-red-500,bg-orange-500,bg-purple-500,bg-pink-500'],
        ];
    }
}
