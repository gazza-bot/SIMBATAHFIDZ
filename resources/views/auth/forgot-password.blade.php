@extends('layouts.guest', ['title' => $title])

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight text-neutral-900 sm:text-[26px]">Lupa Kata Sandi</h1>
    <p class="mt-2 text-sm leading-relaxed text-neutral-500">
        Masukkan username Anda. Admin akan memverifikasi permintaan Anda melalui WhatsApp Center Pesantren.
    </p>
</div>

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    {{-- Username --}}
    <x-input
        name="username"
        id="username"
        label="Username"
        placeholder="Masukkan Username Anda"
        type="text"
        autocomplete="username"
        required
        autofocus
        :value="old('username')"
        :error="$errors->first('username')"
        iconLeft="
            <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'/>
            </svg>
        "
    />

    {{-- Submit --}}
    <x-button type="submit" variant="primary" size="lg" full-width class="mt-2">
        Kirim Permintaan
    </x-button>
</form>

<div class="mt-6 text-center">
    <p class="text-sm text-neutral-500">
        <span class="font-medium">Catatan:</span> Kami tidak mengirim email reset. Hubungi Admin atau WhatsApp Center Pesantren untuk verifikasi.
    </p>
    <a href="{{ route('login') }}" class="mt-3 block text-sm font-medium text-primary-600 hover:text-primary-700">
        Kembali ke Login
    </a>
</div>
@endsection