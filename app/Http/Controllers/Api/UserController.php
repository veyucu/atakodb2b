<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Kullanıcı listesini getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtreleme
        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('musteri_kodu')) {
            $query->where('musteri_kodu', $request->musteri_kodu);
        }

        if ($request->has('plasiyer_kodu')) {
            $query->where('plasiyer_kodu', $request->plasiyer_kodu);
        }

        // Arama
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('musteri_kodu', 'like', "%{$search}%")
                  ->orWhere('musteri_adi', 'like', "%{$search}%");
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Sayfalama
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * Yeni kullanıcı oluştur
     * 
     * @param StoreUserRequest $request
     * @return UserResource
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        
        // Şifreyi hashle
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Varsayılan değerler
        $data['is_active'] = $data['is_active'] ?? true;

        $user = User::create($data);

        return new UserResource($user);
    }

    /**
     * Belirli bir kullanıcıyı getir
     * 
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Kullanıcı bilgilerini güncelle
     * 
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        // Şifre varsa hashle
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return new UserResource($user->fresh());
    }

    /**
     * Kullanıcıyı sil
     * 
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Kullanıcı başarıyla silindi'
        ], 200);
    }

    /**
     * Kullanıcı koduna göre kullanıcı getir
     * 
     * @param Request $request
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function findByCode(Request $request)
    {
        $request->validate([
            'musteri_kodu' => 'required|string'
        ]);

        $user = User::where('musteri_kodu', $request->musteri_kodu)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Kullanıcı bulunamadı'
            ], 404);
        }

        return new UserResource($user);
    }

    /**
     * Toplu kullanıcı oluştur veya güncelle (Sync)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*.musteri_kodu' => 'required|string',
            'users.*.name' => 'required|string',
            'users.*.email' => 'required|email',
        ]);

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($request->users as $userData) {
            try {
                $user = User::where('musteri_kodu', $userData['musteri_kodu'])->first();

                if ($user) {
                    // Güncelle
                    $updateData = $userData;
                    if (isset($updateData['password'])) {
                        $updateData['password'] = Hash::make($updateData['password']);
                    }
                    $user->update($updateData);
                    $updated++;
                } else {
                    // Yeni oluştur
                    $createData = $userData;
                    if (isset($createData['password'])) {
                        $createData['password'] = Hash::make($createData['password']);
                    } else {
                        $createData['password'] = Hash::make('12345678'); // Varsayılan şifre
                    }
                    $createData['is_active'] = $createData['is_active'] ?? true;
                    User::create($createData);
                    $created++;
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'musteri_kodu' => $userData['musteri_kodu'] ?? 'unknown',
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => 'Senkronizasyon tamamlandı',
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors
        ]);
    }
}
