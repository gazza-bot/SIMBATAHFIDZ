<x-layouts.guest :title="$title">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Username --}}
        <x-input
            name="username"
            id="username"
            label="Username"
            placeholder="Masukkan username"
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

        {{-- Password --}}
        <x-input
            name="password"
            id="password"
            label="Password"
            placeholder="Masukkan password"
            type="password"
            autocomplete="current-password"
            required
            :error="$errors->first('password')"
            iconLeft="
                <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6'/>
                </svg>
            "
        />

        {{-- Remember & Forgot --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-neutral-300 text-primary-500 focus:ring-primary-500">
                <span class="text-sm text-neutral-600">Ingat saya</span>
            </label>
            
            <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                Lupa password?
            </a>
        </div>

        {{-- Submit --}}
        <x-button type="submit" variant="primary" size="lg" full-width class="pt-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Masuk ke Akun
        </x-button>

        {{-- Role Info --}}
        <p class="text-center text-xs text-neutral-500">
            Masuk sebagai: Admin, Ustadz, Kepala Tahfidz, atau Wali Santri
        </p>
    </form>
</x-layouts.guest>