@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    @include('admin.partials.master-tabs')

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" href="{{ route('admin.angkatans.index') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </x-button>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Edit Angkatan: {{ $angkatan->nama }}</h2>
                <p class="mt-1 text-sm text-neutral-500">Tahun Mulai: {{ $angkatan->tahun_mulai }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.angkatans.update', $angkatan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card padding="default">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Data Angkatan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input name="nama" label="Nama Angkatan" placeholder="Contoh: 2025/2026" required :error="$errors->first('nama')" :value="old('nama', $angkatan->nama)" />
                    <x-input name="tahun_mulai" label="Tahun Mulai" type="number" placeholder="2025" min="2000" max="{{ date('Y') + 5 }}" required :error="$errors->first('tahun_mulai')" :value="old('tahun_mulai', $angkatan->tahun_mulai)" />
                    <div>
                        <label for="status_aktif" class="block text-sm font-medium text-neutral-700 mb-1.5">
                            Status <span class="text-error-500">*</span>
                        </label>
                        <select name="status_aktif" id="status_aktif" required class="w-full rounded-lg border border-neutral-200 bg-white text-neutral-900 px-3.5 py-2.5 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="1" {{ old('status_aktif', $angkatan->status_aktif) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('status_aktif', $angkatan->status_aktif) ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @if ($errors->first('status_aktif'))
                            <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $errors->first('status_aktif') }}</p>
                        @endif
                    </div>
                </div>
            </x-card>

            <div class="flex justify-end gap-2 pt-2">
                <x-button variant="ghost" href="{{ route('admin.angkatans.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>

@endsection