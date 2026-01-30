<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::whereIn('user_type', ['musteri', 'plasiyer', 'admin']);

        // Arama filtresi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $siteSettings = \App\Models\SiteSetting::getSettings();
        return view('admin.users.index', compact('users', 'siteSettings'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $siteSettings = \App\Models\SiteSetting::getSettings();
        return view('admin.users.create', compact('siteSettings'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'user_type' => 'required|in:admin,plasiyer,musteri',
            'adres' => 'nullable|string',
            'ilce' => 'nullable|string|max:255',
            'il' => 'nullable|string|max:255',
            'plasiyer_kodu' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        $siteSettings = \App\Models\SiteSetting::getSettings();
        return view('admin.users.edit', compact('user', 'siteSettings'));
    }

    /**
     * Update the user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'user_type' => 'required|in:admin,plasiyer,musteri',
            'adres' => 'nullable|string',
            'ilce' => 'nullable|string|max:255',
            'il' => 'nullable|string|max:255',
            'plasiyer_kodu' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['password']);
        $data['is_active'] = $request->has('is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Remove the user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }
}





