<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'sometimes|string|min:6',
            'user_type' => ['sometimes', 'required', Rule::in(['admin', 'plasiyer', 'musteri'])],
            'username' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId)
            ],
            'musteri_kodu' => 'nullable|string|max:255',
            'musteri_adi' => 'nullable|string|max:255',
            'adres' => 'nullable|string',
            'ilce' => 'nullable|string|max:255',
            'il' => 'nullable|string|max:255',
            'gln_numarasi' => 'nullable|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'mail_adresi' => 'nullable|email|max:255',
            'vergi_dairesi' => 'nullable|string|max:255',
            'vergi_kimlik_numarasi' => 'nullable|string|max:255',
            'grup_kodu' => 'nullable|string|max:255',
            'kod1' => 'nullable|string|max:255',
            'kod2' => 'nullable|string|max:255',
            'kod3' => 'nullable|string|max:255',
            'kod4' => 'nullable|string|max:255',
            'kod5' => 'nullable|string|max:255',
            'plasiyer_kodu' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'İsim alanı zorunludur',
            'email.required' => 'E-posta alanı zorunludur',
            'email.email' => 'Geçerli bir e-posta adresi giriniz',
            'email.unique' => 'Bu e-posta adresi zaten kullanılıyor',
            'password.min' => 'Şifre en az 6 karakter olmalıdır',
            'user_type.required' => 'Kullanıcı tipi zorunludur',
            'user_type.in' => 'Geçersiz kullanıcı tipi',
            'username.unique' => 'Bu kullanıcı adı zaten kullanılıyor',
        ];
    }
}
