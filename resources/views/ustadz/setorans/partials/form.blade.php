@php
    $isEdit = $mode === 'edit';
    $jenisValue = old('jenis', $setoran->jenis ?? ($defaultJenis ?? 'ziyadah'));
    $juzValue = old('juz_pilih', $setoran->juz ?? '');
    $surahValue = old('surah_id', $setoran->surah_id ?? '');
    $nilaiValue = old('nilai', $setoran->nilai ?? '');
    $tuntasValue = old('tuntas_lanjut', $setoran->tuntas_lanjut ?? false);

    $nilaiOptions = [
        'mumtaz' => ['label' => 'Mumtaz', 'grade' => 'A'],
        'jayyid_jiddan' => ['label' => 'Jayyid Jiddan', 'grade' => 'B+'],
        'jayyid' => ['label' => 'Jayyid', 'grade' => 'B'],
        'maqbul' => ['label' => 'Maqbul', 'grade' => 'C'],
    ];

    $controlClass = 'w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard min-h-11';
@endphp

<form
    method="POST"
    action="{{ $action }}"
    class="rounded-2xl border border-neutral-200 bg-white shadow-level-1"
    x-data="{ submitting: false }"
    @submit="submitting = true"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="border-b border-neutral-100 px-6 py-5 sm:px-8 sm:py-6">
        <h2 class="text-lg font-bold text-neutral-900">{{ $isEdit ? 'Edit Setoran Hafalan' : 'Formulir Penilaian Setoran' }}</h2>
        <p class="mt-1 text-sm text-neutral-500">Isi data bacaan dan penilaian, lalu simpan.</p>
    </div>

    <div class="space-y-6 p-6 sm:p-8">
        @if ($isEdit)
            <div>
                <x-santri-select
                    name="santri_id"
                    :kelompoks="$kelompoks"
                    :selected="old('santri_id', $setoran->santri_id ?? null)"
                    :error="$errors->first('santri_id')"
                />
                <p class="mt-1.5 text-xs text-neutral-500">Mengubah santri akan memindahkan setoran ini ke santri yang dipilih.</p>
            </div>
        @else
            <input type="hidden" name="santri_id" value="{{ old('santri_id', $selectedSantri->id) }}">
            @error('santri_id')
                <p class="text-sm text-error-600" role="alert">{{ $message }}</p>
            @enderror
        @endif

        <div>
            <label for="tanggal" class="mb-1.5 block text-sm font-medium text-neutral-700">
                Tanggal setoran <span class="ml-1 text-error-500" aria-hidden="true">*</span>
            </label>
            <input
                type="date"
                id="tanggal"
                name="tanggal"
                value="{{ old('tanggal', isset($setoran) ? $setoran->tanggal->format('Y-m-d') : date('Y-m-d')) }}"
                max="{{ date('Y-m-d') }}"
                required
                class="{{ $controlClass }} {{ $errors->first('tanggal') ? 'border-error-500' : '' }}"
                @if ($errors->first('tanggal')) aria-invalid="true" aria-describedby="tanggal-error" @endif
            >
            @error('tanggal')
                <p id="tanggal-error" class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <fieldset>
            <legend class="mb-1.5 block text-sm font-medium text-neutral-700">
                Jenis setoran <span class="ml-1 text-error-500" aria-hidden="true">*</span>
            </legend>
            <div class="grid grid-cols-3 gap-1 rounded-xl bg-neutral-100 p-1">
                @foreach (['ziyadah' => 'Ziyadah', 'murajaah' => 'Murajaah', 'tasmi' => "Tasmi'"] as $value => $label)
                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            name="jenis"
                            value="{{ $value }}"
                            class="peer sr-only"
                            @checked($jenisValue === $value)
                            required
                        >
                        <span class="flex min-h-11 items-center justify-center rounded-lg px-2 py-2 text-center text-sm font-medium text-neutral-600 transition-colors peer-checked:bg-white peer-checked:text-primary-700 peer-checked:shadow-level-1 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500 peer-focus-visible:ring-offset-2">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>
            @error('jenis')
                <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
            @enderror
        </fieldset>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="juz_pilih" class="mb-1.5 block text-sm font-medium text-neutral-700">
                    Juz <span class="ml-1 text-error-500" aria-hidden="true">*</span>
                </label>
                <select
                    id="juz_pilih"
                    name="juz_pilih"
                    required
                    class="{{ $controlClass }} {{ $errors->first('juz_pilih') ? 'border-error-500' : '' }}"
                >
                    <option value="">Pilih juz</option>
                    @for ($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}" @selected((string) $juzValue === (string) $i)>Juz {{ $i }}</option>
                    @endfor
                </select>
                @error('juz_pilih')
                    <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="surah_id" class="mb-1.5 block text-sm font-medium text-neutral-700">
                    Surah <span class="ml-1 text-error-500" aria-hidden="true">*</span>
                </label>
                <select
                    id="surah_id"
                    name="surah_id"
                    required
                    class="{{ $controlClass }} {{ $errors->first('surah_id') ? 'border-error-500' : '' }}"
                >
                    <option value="">Pilih juz terlebih dahulu</option>
                    @if ($juzValue && isset($juzSurahMap[$juzValue]))
                        @foreach ($juzSurahMap[$juzValue] as $item)
                            <option value="{{ $item['id'] }}" @selected((string) $surahValue === (string) $item['id'])>
                                {{ $item['nomor'] }}. {{ $item['nama'] }} (ayat {{ $item['min'] }}-{{ $item['max'] }})
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('surah_id')
                    <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="ayat_awal" class="mb-1.5 block text-sm font-medium text-neutral-700">
                    Ayat mulai <span class="ml-1 text-error-500" aria-hidden="true">*</span>
                </label>
                <div class="flex items-stretch overflow-hidden rounded-lg border border-neutral-200 bg-white focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500">
                    <button type="button" class="flex min-h-11 w-11 flex-shrink-0 items-center justify-center border-r border-neutral-200 text-lg text-neutral-600 hover:bg-neutral-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" data-stepper-target="ayat_awal" data-step="-1" aria-label="Kurangi ayat mulai">-</button>
                    <input
                        type="number"
                        id="ayat_awal"
                        name="ayat_awal"
                        value="{{ old('ayat_awal', $setoran->ayat_awal ?? '') }}"
                        min="1"
                        inputmode="numeric"
                        required
                        class="min-h-11 w-full border-0 bg-transparent px-3 py-2 text-center text-sm font-semibold text-neutral-900 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                    >
                    <button type="button" class="flex min-h-11 w-11 flex-shrink-0 items-center justify-center border-l border-neutral-200 text-lg text-neutral-600 hover:bg-neutral-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" data-stepper-target="ayat_awal" data-step="1" aria-label="Tambah ayat mulai">+</button>
                </div>
                @error('ayat_awal')
                    <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="ayat_akhir" class="mb-1.5 block text-sm font-medium text-neutral-700">
                    Ayat akhir <span class="ml-1 text-error-500" aria-hidden="true">*</span>
                </label>
                <div class="flex items-stretch overflow-hidden rounded-lg border border-neutral-200 bg-white focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500">
                    <button type="button" class="flex min-h-11 w-11 flex-shrink-0 items-center justify-center border-r border-neutral-200 text-lg text-neutral-600 hover:bg-neutral-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" data-stepper-target="ayat_akhir" data-step="-1" aria-label="Kurangi ayat akhir">-</button>
                    <input
                        type="number"
                        id="ayat_akhir"
                        name="ayat_akhir"
                        value="{{ old('ayat_akhir', $setoran->ayat_akhir ?? '') }}"
                        min="1"
                        inputmode="numeric"
                        required
                        class="min-h-11 w-full border-0 bg-transparent px-3 py-2 text-center text-sm font-semibold text-neutral-900 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                    >
                    <button type="button" class="flex min-h-11 w-11 flex-shrink-0 items-center justify-center border-l border-neutral-200 text-lg text-neutral-600 hover:bg-neutral-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" data-stepper-target="ayat_akhir" data-step="1" aria-label="Tambah ayat akhir">+</button>
                </div>
                @error('ayat_akhir')
                    <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <p id="ayat-summary" class="rounded-lg bg-primary-50 px-3 py-2 text-center text-sm font-medium text-primary-700" aria-live="polite">
            Pilih surah untuk melihat jumlah ayat.
        </p>

        <fieldset>
            <legend class="mb-1.5 block text-sm font-medium text-neutral-700">
                Nilai kelancaran dan tajwid <span class="ml-1 text-error-500" aria-hidden="true">*</span>
            </legend>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                @foreach ($nilaiOptions as $value => $option)
                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            name="nilai"
                            value="{{ $value }}"
                            class="peer sr-only"
                            @checked($nilaiValue === $value)
                            required
                        >
                        <span class="flex min-h-14 flex-col items-center justify-center rounded-xl border border-neutral-200 bg-white px-2 py-2 text-center transition-colors peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500 peer-focus-visible:ring-offset-2">
                            <span class="text-sm font-semibold text-neutral-800 peer-checked:text-primary-700">{{ $option['label'] }}</span>
                            <span class="mt-0.5 text-[11px] font-medium text-neutral-500">{{ $option['grade'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('nilai')
                <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
            @enderror
        </fieldset>

        <div>
            <label for="skor" class="mb-1.5 block text-sm font-medium text-neutral-700">Skor angka (opsional)</label>
            <div class="flex items-stretch overflow-hidden rounded-lg border border-neutral-200 bg-white focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500">
                <button type="button" class="flex min-h-11 w-11 flex-shrink-0 items-center justify-center border-r border-neutral-200 text-lg text-neutral-600 hover:bg-neutral-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" data-stepper-target="skor" data-step="-1" aria-label="Kurangi skor">-</button>
                <input
                    type="number"
                    id="skor"
                    name="skor"
                    value="{{ old('skor', $setoran->skor ?? '') }}"
                    min="0"
                    max="100"
                    inputmode="numeric"
                    placeholder="0-100"
                    class="min-h-11 w-full border-0 bg-transparent px-3 py-2 text-center text-sm font-semibold text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                >
                <button type="button" class="flex min-h-11 w-11 flex-shrink-0 items-center justify-center border-l border-neutral-200 text-lg text-neutral-600 hover:bg-neutral-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" data-stepper-target="skor" data-step="1" aria-label="Tambah skor">+</button>
            </div>
            <p class="mt-1 text-xs text-neutral-500">Kosongkan jika tidak menggunakan skor angka.</p>
            @error('skor')
                <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <label for="tuntas_lanjut" class="flex cursor-pointer items-start gap-3 rounded-xl border border-neutral-200 p-5 transition-colors hover:bg-neutral-50">
            <input type="checkbox" id="tuntas_lanjut" name="tuntas_lanjut" value="1" class="peer sr-only" @checked($tuntasValue)>
            <span class="relative mt-0.5 inline-flex h-6 w-11 flex-shrink-0 items-center rounded-full bg-neutral-300 transition-colors peer-checked:bg-primary-500 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500 peer-focus-visible:ring-offset-2 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-transform peer-checked:after:translate-x-5"></span>
            <span class="min-w-0">
                <span class="block text-sm font-medium text-neutral-800">Tuntas dan lanjut ayat berikutnya</span>
                <span class="mt-0.5 block text-xs text-neutral-500">Tandai jika santri siap melanjutkan ke ayat berikutnya.</span>
            </span>
        </label>
        @error('tuntas_lanjut')
            <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
        @enderror

        <div>
            <label for="catatan" class="mb-1.5 block text-sm font-medium text-neutral-700">Catatan musyrif (opsional)</label>
            <textarea
                id="catatan"
                name="catatan"
                rows="4"
                maxlength="1000"
                placeholder="Tulis evaluasi bacaan atau arahan untuk santri."
                class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard {{ $errors->first('catatan') ? 'border-error-500' : '' }}"
            >{{ old('catatan', $setoran->catatan ?? '') }}</textarea>
            @error('catatan')
                <p class="mt-1.5 text-sm text-error-600" role="alert">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-2 border-t border-neutral-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-end sm:px-8">
        <button type="reset" id="reset-setoran" class="inline-flex min-h-11 items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-neutral-600 transition-colors hover:bg-neutral-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
            Bersihkan form
        </button>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-primary-500 px-5 py-2 text-sm font-semibold text-white transition-colors hover:bg-primary-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60" x-bind:disabled="submitting">
            <svg x-show="submitting" x-cloak class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span x-show="!submitting">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Setoran' }}</span>
            <span x-show="submitting" x-cloak>Menyimpan...</span>
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const juzSurahMap = @json($juzSurahMap);
    const form = document.querySelector('form[action="{{ $action }}"]');
    const juzSelect = document.getElementById('juz_pilih');
    const surahSelect = document.getElementById('surah_id');
    const ayatAwal = document.getElementById('ayat_awal');
    const ayatAkhir = document.getElementById('ayat_akhir');
    const summary = document.getElementById('ayat-summary');

    if (!form || !juzSelect || !surahSelect || !ayatAwal || !ayatAkhir || !summary) {
        return;
    }

    function currentJuz() {
        return juzSelect.value;
    }

    function findItem(id) {
        const items = juzSurahMap[currentJuz()] || [];
        return items.find(function (item) { return String(item.id) === String(id); }) || null;
    }

    function fillSurahOptions(juz, selectedId) {
        const items = juzSurahMap[juz] || [];
        surahSelect.innerHTML = '';

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = juz ? 'Pilih surah' : 'Pilih juz terlebih dahulu';
        surahSelect.appendChild(placeholder);

        items.forEach(function (item) {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nomor + '. ' + item.nama + ' (ayat ' + item.min + '-' + item.max + ')';
            if (selectedId && String(selectedId) === String(item.id)) {
                option.selected = true;
            }
            surahSelect.appendChild(option);
        });
    }

    function applyBounds(item) {
        if (!item) {
            ayatAwal.min = 1;
            ayatAwal.max = '';
            ayatAkhir.min = 1;
            ayatAkhir.max = '';
            return;
        }

        ayatAwal.min = item.min;
        ayatAwal.max = item.max;
        ayatAkhir.min = item.min;
        ayatAkhir.max = item.max;
    }

    function updateSummary() {
        const item = findItem(surahSelect.value);

        if (!item) {
            summary.textContent = 'Pilih surah untuk melihat jumlah ayat.';
            return;
        }

        const awal = parseInt(ayatAwal.value, 10);
        const akhir = parseInt(ayatAkhir.value, 10);

        if (!Number.isFinite(awal) || !Number.isFinite(akhir) || akhir < awal) {
            summary.textContent = 'Lengkapi rentang ayat.';
            return;
        }

        const total = akhir - awal + 1;
        const fullSurah = awal === 1 && akhir === item.jumlah_ayat;
        summary.textContent = total + ' ayat' + (fullSurah ? ' | Cover penuh surah' : '');
    }

    function syncSurah(selectedId, keepValues) {
        const item = findItem(selectedId);

        if (!currentJuz()) {
            fillSurahOptions('', '');
            applyBounds(null);
            updateSummary();
            return;
        }

        fillSurahOptions(currentJuz(), selectedId);

        if (!item) {
            applyBounds(null);
            updateSummary();
            return;
        }

        applyBounds(item);

        if (!keepValues) {
            ayatAwal.value = item.min;
            ayatAkhir.value = item.max;
        }

        updateSummary();
    }

    juzSelect.addEventListener('change', function () {
        surahSelect.value = '';
        ayatAwal.value = '';
        ayatAkhir.value = '';
        fillSurahOptions(this.value, '');
        applyBounds(null);
        updateSummary();
    });

    surahSelect.addEventListener('change', function () {
        syncSurah(this.value, false);
    });

    [ayatAwal, ayatAkhir].forEach(function (input) {
        input.addEventListener('input', updateSummary);
        input.addEventListener('change', function () {
            const min = parseInt(input.min || '1', 10);
            const max = parseInt(input.max || '0', 10);

            if (input.value === '') {
                updateSummary();
                return;
            }

            let value = parseInt(input.value, 10);
            if (!Number.isFinite(value)) {
                return;
            }

            if (Number.isFinite(min) && value < min) value = min;
            if (Number.isFinite(max) && max > 0 && value > max) value = max;

            input.value = value;
            updateSummary();
        });
    });

    form.querySelectorAll('[data-stepper-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.stepperTarget);
            if (!input) return;

            const step = parseInt(button.dataset.step, 10);
            const min = parseInt(input.min || '0', 10);
            const max = parseInt(input.max || '100', 10);
            const current = parseInt(input.value, 10);
            const base = Number.isFinite(current) ? current : (input.id === 'skor' ? min : min);
            let next = base + step;

            if (Number.isFinite(min) && next < min) next = min;
            if (Number.isFinite(max) && max > 0 && next > max) next = max;

            input.value = next;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });

    const resetButton = document.getElementById('reset-setoran');
    if (resetButton) {
        resetButton.addEventListener('click', function (event) {
            event.preventDefault();
            form.reset();
            form.querySelectorAll('input[name="nilai"], input[name="tuntas_lanjut"]').forEach(function (input) {
                input.checked = false;
            });
            const jenisDefault = form.querySelector('input[name="jenis"][value="ziyadah"]');
            if (jenisDefault) jenisDefault.checked = true;
    }

    const initialJuz = juzSelect.value;
    const initialSurah = surahSelect.value;
    if (initialJuz) {
        syncSurah(initialSurah, true);
    } else {
        fillSurahOptions('', '');
        applyBounds(null);
        updateSummary();
    }
});
</script>
@endpush
