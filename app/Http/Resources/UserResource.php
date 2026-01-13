<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'user_type' => $this->user_type,
            'musteri_kodu' => $this->musteri_kodu,
            'musteri_adi' => $this->musteri_adi,
            'adres' => $this->adres,
            'ilce' => $this->ilce,
            'il' => $this->il,
            'gln_numarasi' => $this->gln_numarasi,
            'telefon' => $this->telefon,
            'mail_adresi' => $this->mail_adresi,
            'vergi_dairesi' => $this->vergi_dairesi,
            'vergi_kimlik_numarasi' => $this->vergi_kimlik_numarasi,
            'grup_kodu' => $this->grup_kodu,
            'kod1' => $this->kod1,
            'kod2' => $this->kod2,
            'kod3' => $this->kod3,
            'kod4' => $this->kod4,
            'kod5' => $this->kod5,
            'plasiyer_kodu' => $this->plasiyer_kodu,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'last_login_ip' => $this->last_login_ip,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
