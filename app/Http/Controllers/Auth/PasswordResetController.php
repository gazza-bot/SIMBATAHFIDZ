<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showRequest()
    {
        return view('auth.forgot-password', ['title' => 'Lupa Kata Sandi']);
    }

    public function sendRequest(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user) {
            $token = Str::random(64);
            PasswordReset::updateOrCreate(
                ['username' => $user->username],
                [
                    'token' => Hash::make($token),
                    'requested_at' => now(),
                    'token_created_at' => now(),
                ]
            );
        }

        return back()->with('success', 'Permintaan reset dikirim. Hubungi Admin/WhatsApp Center Pesantren untuk verifikasi.');
    }

    public function showReset(Request $request, string $token)
    {
        $reset = PasswordReset::whereNotNull('token')
            ->whereNotNull('token_created_at')
            ->where('token_created_at', '>=', Carbon::now()->subMinutes(config('auth.passwords.users.expire', 60)))
            ->get()
            ->first(function ($reset) use ($token) {
                return Hash::check($token, $reset->token);
            });

        if (!$reset) {
            return redirect()->route('password.request')
                ->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }

        return view('auth.reset-password', [
            'title' => 'Atur Ulang Kata Sandi',
            'token' => $token,
            'username' => $reset->username,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = PasswordReset::where('username', $request->username)->first();

        if (!$reset || !$reset->token || !$reset->token_created_at) {
            return back()->with('error', 'Permintaan reset tidak ditemukan.');
        }

        if (Carbon::parse($reset->token_created_at)->addMinutes(config('auth.passwords.users.expire', 60))->isPast()) {
            $reset->delete();
            return back()->with('error', 'Token sudah kadaluarsa.');
        }

        if (!Hash::check($request->token, $reset->token)) {
            return back()->with('error', 'Token tidak valid.');
        }

        $user = User::where('username', $request->username)->firstOrFail();
        $user->update(['password' => Hash::make($request->password)]);
        $user->tokens()->delete();

        $reset->delete();

        return redirect()->route('login')
            ->with('success', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.');
    }
}