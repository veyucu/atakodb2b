<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_logo',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'gonderim_sekilleri',
    ];

    protected $casts = [
        'gonderim_sekilleri' => 'array',
    ];

    /**
     * Site ayarlarını getir (singleton pattern)
     */
    public static function getSettings()
    {
        return self::first() ?? self::create([
            'site_name' => 'atakodb2b',
            'company_name' => 'Firma Adı',
            'company_address' => 'Firma Adresi',
            'company_phone' => '+90 (XXX) XXX XX XX',
            'company_email' => 'info@example.com',
        ]);
    }

    /**
     * Get the URL for the stored site logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!empty($this->site_logo)) {
            return asset('storage/' . $this->site_logo);
        }

        return null;
    }
}
