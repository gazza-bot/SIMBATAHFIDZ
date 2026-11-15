<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.login', [
            'title' => 'Masuk - SIMBA TAHFIDZ',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('username', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return $this->redirectToDashboard();
        }

        throw ValidationException::withMessages([
            'username' => 'Username atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }

    protected function redirectToDashboard()
    {
        $role = Auth::user()->role;
        
        return match ($role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'ustadz' => redirect()->intended(route('ustadz.dashboard')),
            'kepala_tahfidz' => redirect()->intended(route('kepala.dashboard')),
            'wali' => redirect()->intended(route('wali.dashboard')),
            default => redirect()->route('login')->with('error', 'Peran tidak dikenali.'),
        };
    }
}