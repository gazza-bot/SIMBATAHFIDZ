<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    public function showRequest()
    {
        return view('auth.forgot-password', ['title' => 'Lupa Kata Sandi']);
    }

    public function sendRequest(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user) {
            $token = Str::random(64);
            $selector = substr($token, 0, 8);
            PasswordReset::updateOrCreate(
                ['username' => $user->username],
                [
                    'selector' => $selector,
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
        $selector = substr($token, 0, 8);
        
        $reset = PasswordReset::where('selector', $selector)
            ->whereNotNull('token')
            ->whereNotNull('token_created_at')
            ->where('token_created_at', '>=', Carbon::now()->subMinutes(config('auth.passwords.users.expire', 60)))
            ->first();

        if (!$reset || !Hash::check($token, $reset->token)) {
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

        $selector = substr($request->token, 0, 8);
        $reset = PasswordReset::where('selector', $selector)
            ->where('username', $request->username)
            ->whereNotNull('token')
            ->whereNotNull('token_created_at')
            ->first();

        if (!$reset) {
            return back()->with('error', 'Token tidak valid.');
        }

        if (!Hash::check($request->token, $reset->token)) {
            return back()->with('error', 'Token tidak valid.');
        }

        if (Carbon::parse($reset->token_created_at)->addMinutes(config('auth.passwords.users.expire', 60))->isPast()) {
            $reset->delete();
            return back()->with('error', 'Token sudah kadaluarsa.');
        }

        $user = User::where('username', $request->username)->firstOrFail();

        \DB::transaction(function () use ($user, $reset, $request) {
            $user->update(['password' => Hash::make($request->password)]);
            $reset->delete();
        });

        return redirect()->route('login')
            ->with('success', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.');
    }
}