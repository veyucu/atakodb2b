<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ErpUserController extends Controller
{
    /**
     * ERP'den gelen kullanıcı verilerini kontrol eder (API Key)
     */
    private function validateApiKey(Request $request): bool
    {
        $apiKey = $request->header('X-API-Key');
        $expectedKey = config('services.erp.api_key', 'your-secret-erp-api-key');

        return $apiKey === $expectedKey;
    }

    /**
     * Toplu kullanıcı işlemi (Batch Insert/Update)
     * POST /api/erp/users/batch
     */
    public function batch(Request $request)
    {
        if (!$this->validateApiKey($request)) {
            Log::warning('ERP API: Geçersiz API Key', ['ip' => $request->ip()]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $users = $request->input('users', []);

        if (empty($users)) {
            return response()->json(['success' => false, 'message' => 'Kullanıcı listesi boş'], 400);
        }

        Log::info('ERP API: Batch işlemi başladı', ['count' => count($users)]);

        $inserted = 0;
        $updated = 0;
        $errors = [];
        $successList = []; // Başarılı işlemler listesi

        DB::beginTransaction();

        try {
            foreach ($users as $index => $userData) {
                try {
                    $username = $userData['KullaniciKodu'] ?? null;
                    $isUpdate = ($userData['Islem'] ?? 'I') === 'U';

                    if (empty($username)) {
                        $errors[] = "Index {$index}: KullaniciKodu boş";
                        continue;
                    }

                    $existingUser = User::where('username', $username)->first();

                    if ($isUpdate && $existingUser) {
                        // Güncelleme
                        $updateData = [
                            'name' => $userData['AdSoyad'] ?? $existingUser->name,
                            'adres' => $userData['Adres'] ?? $existingUser->adres,
                            'ilce' => $userData['Ilce'] ?? $existingUser->ilce,
                            'il' => $userData['Il'] ?? $existingUser->il,
                            'plasiyer_kodu' => $userData['PlasiyerKodu'] ?? $existingUser->plasiyer_kodu,
                        ];

                        if (!empty($userData['Sifre'])) {
                            $updateData['password'] = Hash::make($userData['Sifre']);
                        }

                        $existingUser->update($updateData);
                        $updated++;
                        $successList[] = ['username' => $username, 'operation' => 'U'];

                    } elseif (!$existingUser) {
                        // Yeni ekleme
                        User::create([
                            'username' => $username,
                            'name' => $userData['AdSoyad'] ?? '',
                            'email' => $username . '@erp.local',
                            'password' => Hash::make($userData['Sifre'] ?? '123456'),
                            'adres' => $userData['Adres'] ?? null,
                            'ilce' => $userData['Ilce'] ?? null,
                            'il' => $userData['Il'] ?? null,
                            'plasiyer_kodu' => $userData['PlasiyerKodu'] ?? null,
                            'user_type' => 'musteri',
                            'is_active' => true,
                        ]);
                        $inserted++;
                        $successList[] = ['username' => $username, 'operation' => 'I'];

                    } elseif ($existingUser && !$isUpdate) {
                        // Zaten var ama insert isteniyor - güncelle
                        $updateData = [
                            'name' => $userData['AdSoyad'] ?? $existingUser->name,
                            'adres' => $userData['Adres'] ?? $existingUser->adres,
                            'ilce' => $userData['Ilce'] ?? $existingUser->ilce,
                            'il' => $userData['Il'] ?? $existingUser->il,
                            'plasiyer_kodu' => $userData['PlasiyerKodu'] ?? $existingUser->plasiyer_kodu,
                        ];

                        if (!empty($userData['Sifre'])) {
                            $updateData['password'] = Hash::make($userData['Sifre']);
                        }

                        $existingUser->update($updateData);
                        $updated++;
                        $successList[] = ['username' => $username, 'operation' => 'U'];
                    }

                } catch (\Exception $e) {
                    $errors[] = "Index {$index} ({$username}): " . $e->getMessage();
                }
            }

            DB::commit();

            Log::info('ERP API: Batch işlemi tamamlandı', [
                'inserted' => $inserted,
                'updated' => $updated,
                'errors' => count($errors)
            ]);

            return response()->json([
                'success' => true,
                'inserted' => $inserted,
                'updated' => $updated,
                'errorCount' => count($errors),
                'errors' => array_slice($errors, 0, 10), // İlk 10 hata
                'successList' => $successList // Başarılı işlemler
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERP API: Batch işlemi hatası', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'inserted' => $inserted,
                'updated' => $updated
            ], 500);
        }
    }

    /**
     * Yeni kullanıcı oluşturur (ERP'den gelen INSERT)
     */
    public function store(Request $request)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $validated = $request->validate([
                'KullaniciKodu' => 'required|string|max:50|unique:users,username',
                'AdSoyad' => 'required|string|max:255',
                'Adres' => 'nullable|string',
                'Ilce' => 'nullable|string|max:100',
                'Il' => 'nullable|string|max:100',
                'PlasiyerKodu' => 'nullable|string|max:50',
                'Sifre' => 'nullable|string',
            ]);

            $user = User::create([
                'username' => $validated['KullaniciKodu'],
                'name' => $validated['AdSoyad'],
                'email' => $validated['KullaniciKodu'] . '@erp.local',
                'password' => Hash::make($validated['Sifre'] ?? '123456'),
                'adres' => $validated['Adres'] ?? null,
                'ilce' => $validated['Ilce'] ?? null,
                'il' => $validated['Il'] ?? null,
                'plasiyer_kodu' => $validated['PlasiyerKodu'] ?? null,
                'user_type' => 'musteri',
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'data' => ['id' => $user->id, 'username' => $user->username]
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Kullanıcıyı günceller (ERP'den gelen UPDATE)
     */
    public function update(Request $request, string $cariKodu)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $user = User::where('username', $cariKodu)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Kullanıcı bulunamadı'], 404);
            }

            $validated = $request->validate([
                'AdSoyad' => 'required|string|max:255',
                'Adres' => 'nullable|string',
                'Ilce' => 'nullable|string|max:100',
                'Il' => 'nullable|string|max:100',
                'PlasiyerKodu' => 'nullable|string|max:50',
                'Sifre' => 'nullable|string',
            ]);

            $updateData = [
                'name' => $validated['AdSoyad'],
                'adres' => $validated['Adres'] ?? $user->adres,
                'ilce' => $validated['Ilce'] ?? $user->ilce,
                'il' => $validated['Il'] ?? $user->il,
                'plasiyer_kodu' => $validated['PlasiyerKodu'] ?? $user->plasiyer_kodu,
            ];

            if (!empty($validated['Sifre'])) {
                $updateData['password'] = Hash::make($validated['Sifre']);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'data' => ['id' => $user->id, 'username' => $user->username]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
