<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'urun_kodu' => $this->urun_kodu,
            'urun_adi' => $this->urun_adi,
            'barkod' => $this->barkod,
            'muadil_kodu' => $this->muadil_kodu,
            'satis_fiyati' => (float) $this->satis_fiyati,
            'kdv_orani' => (float) $this->kdv_orani,
            'kurum_iskonto' => (float) $this->kurum_iskonto,
            'eczaci_kari' => (float) $this->eczaci_kari,
            'ticari_iskonto' => (float) $this->ticari_iskonto,
            'mf' => $this->mf,
            'depocu_fiyati' => (float) $this->depocu_fiyati,
            'net_fiyat_manuel' => (float) $this->net_fiyat_manuel,
            'net_fiyat' => (float) $this->net_price,
            'total_discount' => (float) $this->total_discount,
            'bakiye' => (float) $this->bakiye,
            'marka' => $this->marka,
            'grup' => $this->grup,
            'kod1' => $this->kod1,
            'kod2' => $this->kod2,
            'kod3' => $this->kod3,
            'kod4' => $this->kod4,
            'kod5' => $this->kod5,
            'urun_resmi' => $this->urun_resmi,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'ozel_liste' => $this->ozel_liste,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
