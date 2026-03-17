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
            'phone'   => ['required', 'string', 'regex:/^\+994[0-9]{9}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Ad daxil edin.',
            'surname.required' => 'Soyad daxil edin.',
            'phone.required'   => 'Telefon nömrəsi daxil edin.',
            'phone.regex'      => 'Telefon nömrəsi +994XXXXXXXXX formatında olmalıdır (məs: +994557038008).',
        ];
    }
}
