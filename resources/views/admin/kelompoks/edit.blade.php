@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    @include('admin.partials.master-tabs')

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" href="{{ route('admin.kelompoks.index') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </x-button>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Edit Kelompok: {{ $kelompok->nama }}</h2>
                <p class="mt-1 text-sm text-neutral-500">Musyrif: {{ $kelompok->ustadz->user->name ?? '—' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.kelompoks.update', $kelompok) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card padding="default">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Data Kelompok</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input name="nama" label="Nama Kelompok" placeholder="Contoh: Halaqah A" required :error="$errors->first('nama')" :value="old('nama', $kelompok->nama)" />
                    <div>
                        <label for="ustadz_id" class="block text-sm font-medium text-neutral-700 mb-1.5">Musyrif (Ustadz)</label>
                        <select name="ustadz_id" id="ustadz_id" class="w-full rounded-lg border border-neutral-200 bg-white text-neutral-900 px-3.5 py-2.5 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="">Pilih Musyrif</option>
                            @foreach ($ustadzs as $ustadz)
                                <option value="{{ $ustadz->id }}" {{ old('ustadz_id', $kelompok->ustadz_id) == $ustadz->id ? 'selected' : '' }}>
                                    {{ $ustadz->nama }} ({{ $ustadz->user->username }})
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->first('ustadz_id'))
                            <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $errors->first('ustadz_id') }}</p>
                        @endif
                    </div>
                    <div>
                        <label for="angkatan_id" class="block text-sm font-medium text-neutral-700 mb-1.5">Angkatan</label>
                        <select name="angkatan_id" id="angkatan_id" class="w-full rounded-lg border border-neutral-200 bg-white text-neutral-900 px-3.5 py-2.5 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="">Pilih Angkatan</option>
                            @foreach ($angkatans as $angkatan)
                                <option value="{{ $angkatan->id }}" {{ old('angkatan_id', $kelompok->angkatan_id) == $angkatan->id ? 'selected' : '' }}>
                                    {{ $angkatan->nama }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->first('angkatan_id'))
                            <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $errors->first('angkatan_id') }}</p>
                        @endif
                    </div>
                </div>
                <x-input name="deskripsi" label="Deskripsi (opsional)" placeholder="Catatan tambahan..." rows="3" :value="old('deskripsi', $kelompok->deskripsi)" />
            </x-card>

            <div class="flex justify-end gap-2 pt-2">
                <x-button variant="ghost" href="{{ route('admin.kelompoks.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>

    {-- Delete Confirmation Modal --}
    <x-modal id="delete-kelompoks" title="Hapus Kelompok" description="Tindakan ini tidak dapat dibatalkan.">
        <p class="text-neutral-700">Apakah Anda yakin ingin menghapus kelompok <span id="delete-kelompoks-name" class="font-medium"></span>?</p>
        <input type="hidden" id="delete-kelompoks-id">
        <div class="mt-6 flex justify-end gap-2">
            <x-button variant="ghost" onclick="closeDeleteKelompokModal()">Batal</x-button>
            <form method="POST" action="{ route('admin.kelompoks.destroy', ':id') }" id="delete-kelompoks-form">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="destructive">Hapus</x-button>
            </form>
        </div>
    </x-modal>

    @push('scripts')
    <script>
        function openDeleteKelompokModal(id, name) {
            document.getElementById('delete-kelompoks-id').value = id;
            document.getElementById('delete-kelompoks-name').textContent = name;
            document.getElementById('delete-kelompoks-form').action = '{ route('admin.kelompoks.destroy', ':id') }'.replace(':id', id);
            document.getElementById('delete-kelompoks').setAttribute('open', '');
        }
        function closeDeleteKelompokModal() {
            document.getElementById('delete-kelompoks').removeAttribute('open');
        }
    </script>
    @endpush
@endsection