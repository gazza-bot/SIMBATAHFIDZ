@extends('layouts.app', ['title' => 'Input Setoran', 'pageTitle' => 'Input Setoran'])

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
            <h2 class="text-lg font-semibold text-neutral-900">Form Input Setoran</h2>
            <p class="mt-1 text-sm text-neutral-500">Lengkapi data setoran hafalan santri.</p>
        </div>

        <form method="POST" action="{{ route('ustadz.setorans.store') }}" class="p-4 sm:p-6 space-y-5">
            @csrf

            <div>
                <x-input
                    type="text"
                    name="santri_search"
                    label="Cari Santri"
                    placeholder="Ketik nama atau NIS santri..."
                    :value="old('santri_search')"
                />
                <div id="santri-dropdown" class="mt-2 hidden bg-white rounded-xl border border-neutral-200 shadow-level-2 max-h-60 overflow-y-auto">
                </div>
                <input type="hidden" name="santri_id" id="santri_id" value="{{ old('santri_id') }}" required>
                <div id="selected-santri" class="mt-2 {{ old('santri_id') ? '' : 'hidden' }}">
                    <div class="flex items-center gap-3 p-3 bg-primary-50 rounded-lg border border-primary-200">
                        <x-avatar name="Santri" size="sm" />
                        <div>
                            <p class="text-sm font-medium text-neutral-900" id="selected-santri-name">-</p>
                            <p class="text-xs text-neutral-500" id="selected-santri-nis">-</p>
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
                        :value="old('tanggal', date('Y-m-d'))"
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
                        <option value="ziyadah" {{ old('jenis') == 'ziyadah' ? 'selected' : '' }}>Ziyadah (Hafalan Baru)</option>
                        <option value="murajaah" {{ old('jenis') == 'murajaah' ? 'selected' : '' }}>Murajaah (Mengulang)</option>
                        <option value="tasmi" {{ old('jenis') == 'tasmi' ? 'selected' : '' }}>Tasmi' / Sima'an (Ujian)</option>
                    </select>
                    @error('jenis')
                        <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="juz_pilih" class="block text-sm font-medium text-neutral-700 mb-1.5">
                        Juz
                        <span class="text-error-500 ml-1" aria-hidden="true">*</span>
                    </label>
                    <select id="juz_pilih" name="juz_pilih" required class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard {{ $errors->first('juz_pilih') ? 'border-error-500' : '' }}">
                        <option value="">Pilih Juz</option>
                        @for ($i = 1; $i <= 30; $i++)
                            <option value="{{ $i }}" {{ old('juz_pilih') == $i ? 'selected' : '' }}>Juz {{ $i }}</option>
                        @endfor
                    </select>
                    @error('juz_pilih')
                        <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="surah_id" class="block text-sm font-medium text-neutral-700 mb-1.5">
                        Surah
                        <span class="text-error-500 ml-1" aria-hidden="true">*</span>
                    </label>
                    <select id="surah_id" name="surah_id" required class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard {{ $errors->first('surah_id') ? 'border-error-500' : '' }}">
                        <option value="">Pilih Juz terlebih dahulu</option>
                    </select>
                    <p id="surah-hint" class="mt-1 text-xs text-neutral-500 hidden"></p>
                    @error('surah_id')
                        <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-input
                        type="number"
                        name="ayat_awal"
                        label="Ayat Awal"
                        placeholder="1"
                        min="1"
                        :value="old('ayat_awal', 1)"
                        required
                        :error="$errors->first('ayat_awal')"
                    />
                    <p id="ayat-awal-hint" class="mt-1 text-xs text-neutral-500 hidden"></p>
                </div>
                <div>
                    <x-input
                        type="number"
                        name="ayat_akhir"
                        label="Ayat Akhir"
                        placeholder="Jumlah ayat"
                        min="1"
                        :value="old('ayat_akhir')"
                        required
                        :error="$errors->first('ayat_akhir')"
                    />
                    <p id="ayat-akhir-hint" class="mt-1 text-xs text-neutral-500 hidden"></p>
                </div>
                <div>
                    <x-input
                        type="number"
                        name="skor"
                        label="Skor Angka"
                        placeholder="0-100"
                        min="0"
                        max="100"
                        :value="old('skor')"
                    />
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="mutqin" name="mutqin" class="w-4 h-4 rounded border-neutral-300 text-primary-500 focus:ring-primary-500" />
                <label for="mutqin" class="text-sm text-neutral-700">
                    Cover penuh porsi juz ini - Set ayat awal & akhir ke batas porsi Juz
                </label>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="tuntas_lanjut" name="tuntas_lanjut" class="w-4 h-4 rounded border-neutral-300 text-primary-500 focus:ring-primary-500" />
                <label for="tuntas_lanjut" class="text-sm text-neutral-700">
                    Tuntas & Berhak Lanjut Ayat Berikutnya
                </label>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_draft" name="is_draft" class="w-4 h-4 rounded border-neutral-300 text-primary-500 focus:ring-primary-500" />
                <label for="is_draft" class="text-sm text-neutral-700">
                    Simpan sebagai Draft
                </label>
            </div>

            <div>
                <label for="nilai" class="block text-sm font-medium text-neutral-700 mb-1.5">
                    Nilai
                    <span class="text-error-500 ml-1" aria-hidden="true">*</span>
                </label>
                <select id="nilai" name="nilai" required class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard {{ $errors->first('nilai') ? 'border-error-500' : '' }}">
                    <option value="">Pilih Nilai</option>
                    <option value="maqbul" {{ old('nilai') == 'maqbul' ? 'selected' : '' }}>Maqbul (Cukup)</option>
                    <option value="jayyid" {{ old('nilai') == 'jayyid' ? 'selected' : '' }}>Jayyid (Baik)</option>
                    <option value="jayyid_jiddan" {{ old('nilai') == 'jayyid_jiddan' ? 'selected' : '' }}>Jayyid Jiddan (Sangat Baik)</option>
                    <option value="mumtaz" {{ old('nilai') == 'mumtaz' ? 'selected' : '' }}>Mumtaz (Luar Biasa)</option>
                </select>
                @error('nilai')
                    <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="catatan" class="block text-sm font-medium text-neutral-700 mb-1.5">Catatan (opsional)</label>
                <textarea id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan tentang setoran ini..." class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-neutral-100">
                <x-button variant="ghost" href="{{ route('ustadz.setorans.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Setoran
                </x-button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const santris = @json($santris);
    const juzSurahMap = @json($juzSurahMap);
    const searchInput = document.querySelector('input[name="santri_search"]');
    const dropdown = document.getElementById('santri-dropdown');
    const hiddenInput = document.getElementById('santri_id');
    const selectedDiv = document.getElementById('selected-santri');
    const clearBtn = document.getElementById('clear-santri');
    const juzSelect = document.getElementById('juz_pilih');
    const surahSelect = document.getElementById('surah_id');
    const surahHint = document.getElementById('surah-hint');
    const ayatAwal = document.querySelector('input[name="ayat_awal"]');
    const ayatAkhir = document.querySelector('input[name="ayat_akhir"]');
    const ayatAwalHint = document.getElementById('ayat-awal-hint');
    const ayatAkhirHint = document.getElementById('ayat-akhir-hint');
    const mutqinCheckbox = document.getElementById('mutqin');

    let currentJuz = juzSelect.value;
    let currentSurahItem = null;

    function fillSurahOptions(juz, selectedId) {
        const items = juzSurahMap[juz] || [];
        surahSelect.innerHTML = '<option value="">Pilih Surah</option>';
        items.forEach(function(s) {
            const option = document.createElement('option');
            option.value = s.id;
            const isLast = s.id === (items[items.length - 1] ? items[items.length - 1].id : s.id);
            option.textContent = s.nomor + '. ' + s.nama + ' (ayat ' + s.min + '-' + s.max + ')';
            surahSelect.appendChild(option);
        });

        if (selectedId) {
            surahSelect.value = selectedId;
            const item = items.find(function(i) { return i.id == selectedId; });
            if (item) {
                setSurahRange(item);
                currentSurahItem = item;
            }
        }
    }

    function setSurahRange(item) {
        currentSurahItem = item;
        ayatAwal.value = item.min;
        ayatAkhir.value = item.max;
        ayatAwal.min = item.min;
        ayatAkhir.min = item.min;
        ayatAkhir.max = item.max;
        ayatAwalHint.textContent = 'Porsi Juz: ayat ' + item.min + '-' + item.max;
        ayatAwalHint.classList.remove('hidden');
        ayatAkhirHint.textContent = 'Batas Juz: ' + item.min + ' - ' + item.max;
        ayatAkhirHint.classList.remove('hidden');
        surahHint.textContent = 'Juz ' + currentJuz + ': ' + item.nomor + '. ' + item.nama + ' (ayat ' + item.min + '-' + item.max + ')';
        surahHint.classList.remove('hidden');
    }

    juzSelect.addEventListener('change', function() {
        const juz = this.value;
        if (!juz) return;
        currentJuz = juz;
        currentSurahItem = null;
        fillSurahOptions(juz, '');
        surahHint.classList.add('hidden');
        ayatAwalHint.classList.add('hidden');
        ayatAkhirHint.classList.add('hidden');
        ayatAwal.value = '';
        ayatAkhir.value = '';
        surahSelect.required = true;
        if (juzSurahMap[juz] && juzSurahMap[juz].length > 0) {
            surahSelect.selectedIndex = 0;
            surahSelect.selectedIndex = 0;
        }
    });

    surahSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (!option || option.value === '') return;
        const id = Number(this.value);
        const item = juzSurahMap[currentJuz] ? juzSurahMap[currentJuz].find(function(s) { return s.id == id; }) : null;
        if (item) {
            setSurahRange(item);
            currentSurahItem = item;
        }
    });

    mutqinCheckbox.addEventListener('change', function() {
        if (this.checked && currentSurahItem) {
            ayatAwal.value = currentSurahItem.min;
            ayatAkhir.value = currentSurahItem.max;
        }
    });

    @if(old('juz_pilih'))
        const oldJuz = @json(old('juz_pilih'));
        const oldSurah = @json(old('surah_id'));
        juzSelect.value = oldJuz;
        if (oldJuz && juzSurahMap[oldJuz]) {
            fillSurahOptions(oldJuz, oldSurah);
            const item = juzSurahMap[oldJuz].find(function(s) { return s.id == oldSurah; });
            if (item) {
                setSurahRange(item);
            }
        }
    @endif

    @if(old('santri_id'))
        const selected = santris.find(function(s) { return s.id == {{ old('santri_id') }}; });
        if (selected) {
            document.getElementById('selected-santri-name').textContent = selected.nama;
            document.getElementById('selected-santri-nis').textContent = selected.nis;
        }
    @elseif($prefillSantriId)
        const prefill = santris.find(function(s) { return s.id == {{ $prefillSantriId }}; });
        if (prefill) {
            document.getElementById('santri_search').value = prefill.nis;
            document.getElementById('selected-santri-name').textContent = prefill.nama;
            document.getElementById('selected-santri-nis').textContent = prefill.nis;
            hiddenInput.value = prefill.id;
            selectedDiv.classList.remove('hidden');
        }
    @endif
});
</script>
@endpush
@endsection