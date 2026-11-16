@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    @include('admin.partials.master-tabs')

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" href="{{ route('admin.walis.index') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </x-button>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Tambah Wali Santri</h2>
                <p class="mt-1 text-sm text-neutral-500">Data wali akan menerima notifikasi mutaba'ah anak.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.walis.store') }}" class="space-y-6">
            @csrf

            <x-card padding="default">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Akun Login</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input name="username" label="Username" placeholder="username wali" required :error="$errors->first('username')" :value="old('username')" />
                    <x-input name="password" label="Password" type="password" placeholder="Min. 6 karakter" required :error="$errors->first('password')" />
                    <x-input name="password_confirmation" label="Konfirmasi Password" type="password" placeholder="Ulangi password" required :error="$errors->first('password_confirmation')" />
                </div>
            </x-card>

            <x-card padding="default">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Data Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input name="nama" label="Nama Lengkap" placeholder="Nama wali" required :error="$errors->first('nama')" :value="old('nama')" />
                    <x-input name="telepon" label="No. Telepon" placeholder="Nomor telepon" :error="$errors->first('telepon')" :value="old('telepon')" />
                    <x-input name="alamat" label="Alamat" placeholder="Alamat lengkap" rows="3" />
                </div>
            </x-card>

            <div class="flex justify-end gap-2 pt-2">
                <x-button variant="ghost" href="{{ route('admin.walis.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Wali
                </x-button>
            </div>
        </form>
    </div>
@endsection