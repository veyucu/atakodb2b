<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Şifre yönetimi için ortak metodlar
 */
trait ManagesPassword
{
    /**
     * Şifreyi hashle (boş değilse)
     */
    protected function hashPassword(?string $password): ?string
    {
        return !empty($password) ? Hash::make($password) : null;
    }

    /**
     * Şifreyi hashle veya mevcut şifreyi koru
     */
    protected function hashPasswordOrKeep(?string $newPassword, ?string $currentPassword): string
    {
        if (!empty($newPassword)) {
            return Hash::make($newPassword);
        }
        return $currentPassword ?? Hash::make(Str::random(16));
    }

    /**
     * Güvenli rastgele şifre oluştur
     */
    protected function generateSecurePassword(int $length = 16): string
    {
        return Hash::make(Str::random($length));
    }

    /**
     * Data array'inde şifre varsa hashle
     */
    protected function hashPasswordInData(array &$data, string $key = 'password'): void
    {
        if (isset($data[$key]) && !empty($data[$key])) {
            $data[$key] = Hash::make($data[$key]);
        }
    }
}
