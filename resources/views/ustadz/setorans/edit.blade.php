@extends('layouts.app', ['title' => 'Edit Setoran', 'pageTitle' => 'Edit Setoran'])

@section('content')

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-2">
            <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </x-button>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-level-1 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-neutral-100">
                <h2 class="text-lg font-semibold text-neutral-900">Edit Setoran</h2>
                <p class="mt-1 text-sm text-neutral-500">Perbarui data setoran hafalan santri.</p>
            </div>

            <form method="POST" action="{{ route('ustadz.setorans.update', $setoran) }}" class="p-4 sm:p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input
                        type="text"
                        name="santri_search"
                        label="Cari Santri"
                        placeholder="Ketik nama atau NIS santri..."
                        :value="old('santri_search', $setoran->santri->nama ?? '')"
                    />
                    <div id="santri-dropdown" class="mt-2 hidden bg-white rounded-xl border border-neutral-200 shadow-level-2 max-h-60 overflow-y-auto">
                    </div>
                    <input type="hidden" name="santri_id" id="santri_id" value="{{ old('santri_id', $setoran->santri_id) }}" required>
                    <div id="selected-santri" class="mt-2">
                        <div class="flex items-center gap-3 p-3 bg-primary-50 rounded-lg border border-primary-200">
                            <x-avatar :name="$setoran->santri->nama ?? 'Santri'" size="sm" />
                            <div>
                                <p class="text-sm font-medium text-neutral-900" id="selected-santri-name">{{ $setoran->santri->nama ?? '-' }}</p>
                                <p class="text-xs text-neutral-500" id="selected-santri-nis">{{ $setoran->santri->nis ?? '-' }}</p>
                            </div>
                            <button type="button" id="clear-santri" class="ml-auto p-1 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-primary-500" aria-label="Hapus pilihan santri">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    @error('santri_id')
                        <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input
                            type="date"
                            name="tanggal"
                            label="Tanggal Setoran"
                            :value="old('tanggal', $setoran->tanggal->format('Y-m-d'))"
                            required
                            :error="$errors->first('tanggal')"
                        />
                    </div>
                    <div>
                        <label for="jenis" class="block text-sm font-medium text-neutral-700 mb-1.5">
                            Jenis Setoran
                            <span class="text-error-500 ml-1" aria-hidden="true">*</span>
                        </label>
                        <select id="jenis" name="jenis" required class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard {{ $errors->first('jenis') ? 'border-error-500' : '' }}">
                            <option value="">Pilih Jenis</option>
                            <option value="ziyadah" {{ old('jenis', $setoran->jenis) == 'ziyadah' ? 'selected' : '' }}>Ziyadah (Hafalan Baru)</option>
                            <option value="murajaah" {{ old('jenis', $setoran->jenis) == 'murajaah' ? 'selected' : '' }}>Murajaah (Mengulang)</option>
                        </select>
                        @error('jenis')
                            <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="surah_id" class="block text-sm font-medium text-neutral-700 mb-1.5">
                        Surah
                        <span class="text-error-500 ml-1" aria-hidden="true">*</span>
                    </label>
                    <select id="surah_id" name="surah_id" required class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard {{ $errors->first('surah_id') ? 'border-error-500' : '' }}">
                        <option value="">Pilih Surah</option>
                        @foreach ($surahs as $surah)
                            <option value="{{ $surah->id }}" data-ayat="{{ $surah->jumlah_ayat }}" data-juz="{{ $surah->juz_mulai }}" {{ old('surah_id', $setoran->surah_id) == $surah->id ? 'selected' : '' }}>
                                {{ $surah->nomor }}. {{ $surah->nama_latin }} ({{ $surah->jumlah_ayat }} ayat, Juz {{ $surah->juz_mulai }})
                            </option>
                        @endforeach
                    </select>
                    @error('surah_id')
                        <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input
                            type="number"
                            name="ayat_awal"
                            label="Ayat Awal"
                            placeholder="1"
                            min="1"
                            :value="old('ayat_awal', $setoran->ayat_awal)"
                            required
                            :error="$errors->first('ayat_awal')"
                        />
                    </div>
                    <div>
                        <x-input
                            type="number"
                            name="ayat_akhir"
                            label="Ayat Akhir"
                            placeholder="Jumlah ayat"
                            min="1"
                            :value="old('ayat_akhir', $setoran->ayat_akhir)"
                            required
                            :error="$errors->first('ayat_akhir')"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="mutqin" name="mutqin" class="w-4 h-4 rounded border-neutral-300 text-primary-500 focus:ring-primary-500" {{ $setoran->ayat_awal == 1 && $setoran->ayat_akhir == ($setoran->surah->jumlah_ayat ?? 0) ? 'checked' : '' }} />
                    <label for="mutqin" class="text-sm text-neutral-700">
                        Cover penuh (Mutqin) - Set ayat awal = 1 dan ayat akhir = jumlah ayat surah
                    </label>
                </div>

                <div>
                    <label for="nilai" class="block text-sm font-medium text-neutral-700 mb-1.5">
                        Nilai
                        <span class="text-error-500 ml-1" aria-hidden="true">*</span>
                    </label>
                    <select id="nilai" name="nilai" required class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard {{ $errors->first('nilai') ? 'border-error-500' : '' }}">
                        <option value="">Pilih Nilai</option>
                        <option value="maqbul" {{ old('nilai', $setoran->nilai) == 'maqbul' ? 'selected' : '' }}>Maqbul (Cukup)</option>
                        <option value="jayyid" {{ old('nilai', $setoran->nilai) == 'jayyid' ? 'selected' : '' }}>Jayyid (Baik)</option>
                        <option value="jayyid_jiddan" {{ old('nilai', $setoran->nilai) == 'jayyid_jiddan' ? 'selected' : '' }}>Jayyid Jiddan (Sangat Baik)</option>
                        <option value="mumtaz" {{ old('nilai', $setoran->nilai) == 'mumtaz' ? 'selected' : '' }}>Mumtaz (Luar Biasa)</option>
                    </select>
                    @error('nilai')
                        <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="catatan" class="block text-sm font-medium text-neutral-700 mb-1.5">Catatan (opsional)</label>
                    <textarea id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan tentang setoran ini..." class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard">{{ old('catatan', $setoran->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-neutral-100">
                    <x-button variant="ghost" href="{{ route('ustadz.setorans.index') }}">Batal</x-button>
                    <x-button type="submit" variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const santris = @json($santris);
        const searchInput = document.querySelector('input[name="santri_search"]');
        const dropdown = document.getElementById('santri-dropdown');
        const hiddenInput = document.getElementById('santri_id');
        const selectedDiv = document.getElementById('selected-santri');
        const clearBtn = document.getElementById('clear-santri');
        const surahSelect = document.getElementById('surah_id');
        const ayatAwal = document.querySelector('input[name="ayat_awal"]');
        const ayatAkhir = document.querySelector('input[name="ayat_akhir"]');
        const mutqinCheckbox = document.getElementById('mutqin');

        function renderDropdown(items) {
            if (items.length === 0) {
                dropdown.innerHTML = '<p class="p-3 text-sm text-neutral-500">Tidak ada santri ditemukan.</p>';
            } else {
                dropdown.innerHTML = items.map(function(s) {
                    return '<button type="button" class="w-full text-left px-3 py-2 text-sm hover:bg-neutral-50 focus:bg-neutral-50 focus:outline-none transition-colors flex items-center gap-2" data-id="' + s.id + '" data-name="' + s.nama + '" data-nis="' + s.nis + '">' +
                        '<div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-xs font-medium text-primary-700">' + s.nama.charAt(0) + '</div>' +
                        '<div><p class="font-medium text-neutral-900">' + s.nama + '</p><p class="text-xs text-neutral-500">' + s.nis + '</p></div>' +
                        '</button>';
                }).join('');
            }
            dropdown.classList.remove('hidden');
        }

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const filtered = santris.filter(function(s) {
                return s.nama.toLowerCase().indexOf(query) !== -1 || s.nis.indexOf(query) !== -1;
            });
            renderDropdown(filtered.slice(0, 10));
        });

        searchInput.addEventListener('focus', function() {
            if (this.value.length > 0) {
                const query = this.value.toLowerCase();
                const filtered = santris.filter(function(s) {
                    return s.nama.toLowerCase().indexOf(query) !== -1 || s.nis.indexOf(query) !== -1;
                });
                renderDropdown(filtered.slice(0, 10));
            }
        });

        dropdown.addEventListener('click', function(e) {
            const btn = e.target.closest('button[data-id]');
            if (!btn) return;
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const nis = btn.dataset.nis;
            hiddenInput.value = id;
            document.getElementById('selected-santri-name').textContent = name;
            document.getElementById('selected-santri-nis').textContent = nis;
            selectedDiv.classList.remove('hidden');
            dropdown.classList.add('hidden');
            searchInput.value = '';
        });

        clearBtn.addEventListener('click', function() {
            hiddenInput.value = '';
            selectedDiv.classList.add('hidden');
            searchInput.focus();
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== searchInput) {
                dropdown.classList.add('hidden');
            }
        });

        surahSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const maxAyat = option.dataset.ayat;
            if (maxAyat) {
                ayatAkhir.placeholder = maxAyat + ' (maks ' + maxAyat + ')';
                ayatAkhir.max = maxAyat;
            }
        });

        mutqinCheckbox.addEventListener('change', function() {
            if (this.checked) {
                const option = surahSelect.options[surahSelect.selectedIndex];
                const maxAyat = option.dataset.ayat;
                if (maxAyat) {
                    ayatAwal.value = 1;
                    ayatAkhir.value = maxAyat;
                }
            }
        });
    });
    </script>
    @endpush
@endsection