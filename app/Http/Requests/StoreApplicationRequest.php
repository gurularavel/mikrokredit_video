<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:100'],
            'phone'   => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Ad daxil edin.',
            'surname.required' => 'Soyad daxil edin.',
            'phone.required'   => 'Telefon nömrəsi daxil edin.',
            'phone.regex'      => 'Telefon nömrəsi düzgün formatda deyil.',
        ];
    }
}
