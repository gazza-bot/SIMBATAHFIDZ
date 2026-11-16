@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" href="{{ route('admin.santris.index') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </x-button>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Edit Santri: {{ $santri->nama }}</h2>
                <p class="mt-1 text-sm text-neutral-500">NIS: {{ $santri->nis }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.santris.update', $santri) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card padding="default">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Data Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input name="nis" label="NIS" placeholder="Contoh: 2024001" required :error="$errors->first('nis')" :value="old('nis', $santri->nis)" />
                    <x-input name="nama" label="Nama Lengkap" placeholder="Nama lengkap santri" required :error="$errors->first('nama')" :value="old('nama', $santri->nama)" />
                    <x-input name="tanggal_masuk" label="Tanggal Masuk" type="date" :value="old('tanggal_masuk', $santri->tanggal_masuk?->format('Y-m-d'))" :error="$errors->first('tanggal_masuk')" />
                    <div>
                        <label for="status_aktif" class="block text-sm font-medium text-neutral-700 mb-1.5">
                            Status Aktif <span class="text-error-500">*</span>
                        </label>
                        <select name="status_aktif" id="status_aktif" required class="w-full rounded-lg border border-neutral-200 bg-white text-neutral-900 px-3.5 py-2.5 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="aktif" {{ old('status_aktif', $santri->status_aktif) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status_aktif', $santri->status_aktif) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="lulus" {{ old('status_aktif', $santri->status_aktif) === 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="pindah" {{ old('status_aktif', $santri->status_aktif) === 'pindah' ? 'selected' : '' }}>Pindah</option>
                        </select>
                        @if ($errors->first('status_aktif'))
                            <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $errors->first('status_aktif') }}</p>
                        @endif
                    </div>
                </div>
            </x-card>

            <x-card padding="default">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Relasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="angkatan_id" class="block text-sm font-medium text-neutral-700 mb-1.5">
                            Angkatan <span class="text-error-500">*</span>
                        </label>
                        <select name="angkatan_id" id="angkatan_id" required class="w-full rounded-lg border border-neutral-200 bg-white text-neutral-900 px-3.5 py-2.5 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="">Pilih Angkatan</option>
                            @foreach ($angkatans as $angkatan)
                                <option value="{{ $angkatan->id }}" {{ old('angkatan_id', $santri->angkatan_id) == $angkatan->id ? 'selected' : '' }}>{{ $angkatan->nama }}</option>
                            @endforeach
                        </select>
                        @if ($errors->first('angkatan_id'))
                            <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $errors->first('angkatan_id') }}</p>
                        @endif
                    </div>
                    <div>
                        <label for="kelompok_id" class="block text-sm font-medium text-neutral-700 mb-1.5">Kelompok Halaqah</label>
                        <select name="kelompok_id" id="kelompok_id" class="w-full rounded-lg border border-neutral-200 bg-white text-neutral-900 px-3.5 py-2.5 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="">Belum ada kelompok</option>
                            @foreach ($kelompoks as $kelompok)
                                <option value="{{ $kelompok->id }}" {{ old('kelompok_id', $santri->kelompok_id) == $kelompok->id ? 'selected' : '' }}>{{ $kelompok->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="wali_id" class="block text-sm font-medium text-neutral-700 mb-1.5">Wali Santri</label>
                        <select name="wali_id" id="wali_id" class="w-full rounded-lg border border-neutral-200 bg-white text-neutral-900 px-3.5 py-2.5 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="">Belum ada wali</option>
                            @foreach ($walis as $wali)
                                <option value="{{ $wali->id }}" {{ old('wali_id', $santri->wali_id) == $wali->id ? 'selected' : '' }}>{{ $wali->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-card>

            <x-card padding="default">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Foto Santri</h3>
                <div class="flex items-center gap-4">
                    <div class="h-20 w-20 rounded-xl overflow-hidden bg-neutral-100">
                        @if ($santri->foto)
                            <img src="{{ asset('storage/' . $santri->foto) }}" alt="Foto {{ $santri->nama }}" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center text-neutral-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label for="foto" class="block text-sm font-medium text-neutral-700 mb-1.5">Ganti Foto (opsional)</label>
                        <input type="file" name="foto" id="foto" accept="image/*" class="block w-full text-sm text-neutral-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100 cursor-pointer">
                        <p class="mt-1.5 text-xs text-neutral-500">Format JPG/PNG, maksimal 2MB. Kosongkan jika tidak ingin mengubah.</p>
                    </div>
                </div>
            </x-card>

            <div class="flex justify-end gap-2 pt-2">
                <x-button variant="ghost" href="{{ route('admin.santris.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>
@endsection