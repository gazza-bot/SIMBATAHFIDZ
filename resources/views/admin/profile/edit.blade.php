@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <h2 class="text-xl font-bold text-neutral-900">Profil Admin</h2>
            <p class="mt-1 text-sm text-neutral-500">Perbarui nama, username, atau password akun Anda.</p>
        </div>

        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card padding="default">
                <div class="flex items-center gap-4 pb-5 border-b border-neutral-100">
                    <x-avatar :name="$user->name" size="xl" />
                    <div>
                        <p class="text-lg font-semibold text-neutral-900">{{ $user->name }}</p>
                        <p class="text-sm text-neutral-500">Administrator SIMBA TAHFIDZ</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <x-input name="name" label="Nama Lengkap" required :value="old('name', $user->name)" :error="$errors->first('name')" />
                    <x-input name="username" label="Username" required :value="old('username', $user->username)" :error="$errors->first('username')" />
                </div>
            </x-card>

            <x-card padding="default">
                <h3 class="text-lg font-semibold text-neutral-900">Ubah Password</h3>
                <p class="mt-1 text-sm text-neutral-500">Kosongkan jika password tidak ingin diubah.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <x-input name="password" label="Password Baru" type="password" :error="$errors->first('password')" />
                    <x-input name="password_confirmation" label="Konfirmasi Password" type="password" :error="$errors->first('password_confirmation')" />
                </div>
            </x-card>

            <div class="flex justify-end">
                <x-button type="submit" variant="primary">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>
@endsection
