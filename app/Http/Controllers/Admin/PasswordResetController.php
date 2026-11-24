<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function index()
    {
        $requests = PasswordReset::with('user')
            ->latest('id')
            ->paginate(20);

        return view('admin.password-requests.index', [
            'title' => 'Permintaan Reset Password',
            'pageTitle' => 'Permintaan Reset Password',
            'requests' => $requests,
        ]);
    }

    public function issue(User $user)
    {
        $token = Str::random(64);

        PasswordReset::updateOrCreate(
            ['username' => $user->username],
            [
                'token' => Hash::make($token),
                'requested_at' => now(),
                'token_created_at' => now(),
            ]
        );

        return back()->with([
            'success' => 'Link reset telah dibuat. Silakan salin link berikut untuk '.$user->name.'.',
            'reset_link' => route('password.reset', ['token' => $token, 'username' => $user->username]),
        ]);
    }

    public function destroy(User $user)
    {
        PasswordReset::where('username', $user->username)->delete();

        return back()->with('success', 'Permintaan reset password dihapus.');
    }
}