<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        return [
            'urun_kodu' => 'required|string|max:255|unique:products,urun_kodu',
            'urun_adi' => 'required|string|max:255',
            'barkod' => 'nullable|string|max:255',
            'muadil_kodu' => 'nullable|string|max:255',
            'satis_fiyati' => 'required|numeric|min:0',
            'kdv_orani' => 'nullable|numeric|min:0|max:100',
            'kurum_iskonto' => 'nullable|numeric|min:0|max:100',
            'eczaci_kari' => 'nullable|numeric|min:0|max:100',
            'ticari_iskonto' => 'nullable|numeric|min:0|max:100',
            'mf' => 'nullable|string|max:255',
            'depocu_fiyati' => 'nullable|numeric|min:0',
            'net_fiyat_manuel' => 'nullable|numeric|min:0',
            'bakiye' => 'nullable|numeric',
            'marka' => 'nullable|string|max:255',
            'grup' => 'nullable|string|max:255',
            'kod1' => 'nullable|string|max:255',
            'kod2' => 'nullable|string|max:255',
            'kod3' => 'nullable|string|max:255',
            'kod4' => 'nullable|string|max:255',
            'kod5' => 'nullable|string|max:255',
            'urun_resmi' => 'nullable|string',
            'is_active' => 'boolean',
            'ozel_liste' => 'boolean',
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
            'urun_kodu.required' => 'Ürün kodu zorunludur',
            'urun_kodu.unique' => 'Bu ürün kodu zaten kullanılıyor',
            'urun_adi.required' => 'Ürün adı zorunludur',
            'satis_fiyati.required' => 'Satış fiyatı zorunludur',
            'satis_fiyati.numeric' => 'Satış fiyatı sayısal olmalıdır',
            'satis_fiyati.min' => 'Satış fiyatı 0\'dan küçük olamaz',
        ];
    }
}
