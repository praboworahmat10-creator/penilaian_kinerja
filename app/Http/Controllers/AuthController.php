<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (session()->has('user_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        $user = DB::table('users')->where('username', $credentials['username'])->first();
        $validPassword = false;

        if ($user !== null && preg_match('/\A[a-f0-9]{32}\z/i', $user->password)) {
            $validPassword = hash_equals(strtolower($user->password), md5($credentials['password']));
        } elseif ($user !== null) {
            $validPassword = Hash::check($credentials['password'], $user->password);
        }

        if (!$validPassword) {
            return back()
                ->withErrors(['username' => 'Username atau password salah.'])
                ->onlyInput('username');
        }

        if (preg_match('/\A[a-f0-9]{32}\z/i', $user->password)) {
            DB::table('users')->where('id', $user->id)->update([
                'password' => Hash::make($credentials['password']),
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put([
            'user_id' => $user->id,
            'username' => $user->username,
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
