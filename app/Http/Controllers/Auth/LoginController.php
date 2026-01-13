<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        // Kullanıcı adı veya email ile giriş yapabilmek için
        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->username,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Son giriş bilgilerini güncelle
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Login aktivitesini kaydet (sadece müşteri için)
            if ($user->user_type === 'musteri') {
                \App\Models\CustomerActivity::logLogin($user->id);
            }

            // Her login'de kampanya popup'ını göstermek için session flag
            session(['show_campaign_popup' => true]);

            // Redirect based on user type
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->isPlasiyer()) {
                // Plasiyer için müşteri seçim ekranına yönlendir
                return redirect()->route('plasiyer.selectCustomer');
            } else {
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'username' => 'Kullanıcı adı veya şifre hatalı.',
        ])->onlyInput('username');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}






