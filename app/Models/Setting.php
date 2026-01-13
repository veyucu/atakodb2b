<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_name',
        'site_logo',
    ];

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




















