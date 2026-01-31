<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'username',
        'adres',
        'ilce',
        'il',
        'plasiyer_kodu',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if user is plasiyer.
     */
    public function isPlasiyer(): bool
    {
        return $this->user_type === 'plasiyer';
    }

    /**
     * Check if user is customer.
     */
    public function isMusteri(): bool
    {
        return $this->user_type === 'musteri';
    }

    /**
     * Get the cart items for the user.
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get customers assigned to this plasiyer.
     * Plasiyer'in plasiyer_kodu'su, müşterilerin plasiyer_kodu field'i ile eşleşir
     */
    public function customers()
    {
        return $this->hasMany(User::class, 'plasiyer_kodu', 'plasiyer_kodu')
            ->where('user_type', 'musteri')
            ->where('is_active', true);
    }

    /**
     * Get the plasiyer for this customer.
     */
    public function plasiyer()
    {
        return $this->belongsTo(User::class, 'plasiyer_kodu', 'plasiyer_kodu')
            ->where('user_type', 'plasiyer');
    }
}






