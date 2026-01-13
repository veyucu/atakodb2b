<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * API Token oluştur (Login)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'string|max:255'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Girilen bilgiler hatalı.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Hesabınız aktif değil.'
            ], 403);
        }

        // Token oluştur
        $token = $user->createToken($request->device_name ?? 'api-token')->plainTextToken;

        return response()->json([
            'message' => 'Giriş başarılı',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'musteri_kodu' => $user->musteri_kodu,
            ]
        ]);
    }

    /**
     * Token iptal et (Logout)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Mevcut token'ı sil
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Çıkış başarılı'
        ]);
    }

    /**
     * Tüm token'ları iptal et
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutAll(Request $request)
    {
        // Kullanıcının tüm token'larını sil
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Tüm oturumlar sonlandırıldı'
        ]);
    }

    /**
     * Kullanıcı bilgilerini getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }
}
