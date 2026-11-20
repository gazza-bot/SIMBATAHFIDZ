@extends('layouts.app', ['title' => 'Presensi Santri', 'pageTitle' => 'Presensi Santri Halaqah'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="presensiModal()" x-init="init()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-neutral-900">Presensi Santri Halaqah</h2>
            <p class="text-sm text-neutral-500">Halaqah {{ \App\Models\Kelompok::where('ustadz_id', auth()->user()->ustadz->id)->first()?->nama ?? 'A' }}</p>
        </div>
        <form method="GET" action="{{ route('ustadz.absensis.index') }}" class="flex flex-col sm:flex-row gap-2">
            <input
                type="date"
                name="tanggal"
                value="{{ $date }}"
                id="filter-tanggal"
                class="w-full sm:w-auto rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2"
                onchange="this.form.submit()"
                required
                aria-label="Filter tanggal presensi"
            >
            <select
                name="sesi"
                id="filter-sesi"
                class="w-full sm:w-auto rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2"
                onchange="this.form.submit()"
                aria-label="Filter sesi presensi"
            >
                <option value="subuh" {{ $sesi == 'subuh' ? 'selected' : '' }}>Subuh</option>
                <option value="ashar" {{ $sesi == 'ashar' ? 'selected' : '' }}>Ashar</option>
                <option value="maghrib" {{ $sesi == 'maghrib' ? 'selected' : '' }}>Maghrib</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4 p-2" role="region" aria-label="Statistik presensi hari ini">
        <div>
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Total</p>
            <p class="text-2xl font-bold text-neutral-900">{{ $stats['total'] }}</p>
        </div>
        <div class="text-right sm:text-left">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Hadir</p>
            <p class="text-2xl font-bold text-success-600">{{ $stats['hadir'] }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Izin</p>
            <p class="text-2xl font-bold text-info-600">{{ $stats['izin'] }}</p>
        </div>
        <div class="text-right sm:text-left">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Sakit/Alpa</p>
            <p class="text-2xl font-bold text-warning-600">{{ $stats['sakit'] + $stats['alpa'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden" data-table-container>
        <div class="overflow-x-auto">
            <table class="w-full" role="grid" aria-label="Daftar presensi santri">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-10">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama Santri</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">NIS</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Angkatan</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Waktu Tiba</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100" x-ref="tableBody">
                    @forelse ($santris as $index => $santri)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-neutral-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar name="{{ $santri->nama }}" size="sm" />
                                    <div>
                                        <p class="text-sm text-neutral-900">{{ $santri->nama }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $santri->nis }}</td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $santri->angkatan->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-xs text-neutral-500">
                                @if (isset($existingAbsensi[$santri->id]))
                                    {{ $existingAbsensi[$santri->id]->waktu_tiba ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if (isset($existingAbsensi[$santri->id]))
                                    <x-badge variant="{{ $existingAbsensi[$santri->id]->status == 'hadir' ? 'success' : ($existingAbsensi[$santri->id]->status == 'izin' ? 'info' : ($existingAbsensi[$santri->id]->status == 'sakit' ? 'warning' : 'error')) }}" size="sm" dot>
                                        {{ $existingAbsensi[$santri->id]->status }}
                                    </x-badge>
                                @else
                                    <x-button variant="secondary" size="sm" type="button" @click="openModal({{ $santri->id }})" class="min-h-[44px]">
                                        Simpan
                                    </x-button>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    @if (!isset($existingAbsensi[$santri->id]))
                                        <x-button variant="ghost" size="sm" type="button" @click="openModal({{ $santri->id }})" class="min-h-[44px]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Tambah
                                        </x-button>
                                    @else
                                        <x-button variant="ghost" size="sm" type="button" @click="editModal({{ $santri->id }})" class="min-h-[44px]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </x-button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full border-2 border-dashed border-neutral-200 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm0 0a4 4 0 014 4v1h-8v-1a4 4 0 014-4z"/></svg>
                                    </div>
                                    <p class="text-neutral-500">Belum ada santri di halaqah ini.</p>
                                    <x-button variant="primary" size="sm" href="{{ route('admin.santris.create') }}">Tambah Santri</x-button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <!-- Loading skeleton rows (hidden by default, shown via JS) -->
                    <template x-if="isLoading">
                        <tr class="animate-pulse" x-for="i in 5" :key="i">
                            <td class="px-4 py-3"><div class="h-4 w-8 bg-neutral-200 rounded"></div></td>
                            <td class="px-4 py-3"><div class="h-4 w-32 bg-neutral-200 rounded"></div></td>
                            <td class="px-4 py-3"><div class="h-4 w-20 bg-neutral-200 rounded"></div></td>
                            <td class="px-4 py-3"><div class="h-4 w-16 bg-neutral-200 rounded"></div></td>
                            <td class="px-4 py-3"><div class="h-4 w-16 bg-neutral-200 rounded"></div></td>
                            <td class="px-4 py-3"><div class="h-4 w-16 bg-neutral-200 rounded mx-auto"></div></td>
                            <td class="px-4 py-3"><div class="h-8 w-20 bg-neutral-200 rounded mx-auto"></div></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div class="fixed inset-0 z-50 flex items-center justify-center" x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="fixed inset-0 bg-neutral-950/70 backdrop-blur-sm" @click="closeModal" aria-hidden="true" tabindex="-1"></div>
        <div class="relative w-full max-w-md mx-4 bg-white rounded-2xl shadow-level-4 p-6" x-trap="showModal" x-ref="modalContent">
            <div class="flex items-start justify-between mb-4">
                <h3 id="modal-title" class="text-lg font-semibold text-neutral-900" x-text="currentAction === 'add' ? 'Tambah Presensi' : 'Edit Presensi'"></h3>
                <button type="button" @click="closeModal" @keydown.escape.window="closeModal" class="p-2 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label for="modal-santri" class="block text-sm font-medium text-neutral-700 mb-1.5">Santri</label>
                    <select id="modal-santri" name="absensi" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 min-h-[44px]" x-model="selectedSantri" required>
                        <option value="">Pilih Santri</option>
                        @foreach ($santris as $santri)
                            <option value="{{ $santri->id }}" data-nama="{{ $santri->nama }}" data-nis="{{ $santri->nis }}" data-angkatan="{{ $santri->angkatan->nama ?? '' }}">{{ $santri->nama }} ({{ $santri->nis }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="modal-status" class="block text-sm font-medium text-neutral-700 mb-1.5">Status</label>
                    <select id="modal-status" name="absensi[{{ $santriId ?? 0 }}][status]" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 min-h-[44px]" x-model="selectedStatus" required>
                        <option value="">Pilih Status</option>
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpa">Alpa</option>
                    </select>
                </div>
                <div>
                    <label for="modal-waktu" class="block text-sm font-medium text-neutral-700 mb-1.5">Waktu Tiba (opsional)</label>
                    <input type="time" id="modal-waktu" name="absensi[{{ $santriId ?? 0 }}][waktu_tiba]" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 min-h-[44px]" x-model="waktuTiba">
                </div>
                <div>
                    <label for="modal-catatan" class="block text-sm font-medium text-neutral-700 mb-1.5">Keterangan (opsional)</label>
                    <textarea id="modal-catatan" name="absensi[{{ $santriId ?? 0 }}][catatan]" rows="2" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 min-h-[44px]" x-model="catatan"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="ghost" @click="closeModal" class="min-h-[44px]">Batal</x-button>
                <x-button type="button" variant="primary" @click="savePresensi" class="min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan
                </x-button>
            </div>
        </div>
    </div>

    @if (session('success'))
        <x-alert variant="success">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert variant="error">{{ session('error') }}</x-alert>
    @endif
</div>

<script>
function presensiModal() {
    return {
        showModal: false,
        currentAction: 'add',
        selectedSantri: '',
        selectedStatus: '',
        waktuTiba: '',
        catatan: '',
        currentSantriId: null,
        isLoading: false,
        existingAbsensi: {},
        lastFocusedElement: null,
        init() {
            this.existingAbsensi = @json($existingAbsensi);
            // Listen for filter form submissions to show loading state
            document.getElementById('filter-tanggal')?.addEventListener('change', () => this.isLoading = true);
            document.getElementById('filter-sesi')?.addEventListener('change', () => this.isLoading = true);
        },
        setMode(action) {
            this.currentAction = action;
            this.selectedSantri = '';
            this.selectedStatus = '';
            this.waktuTiba = '';
            this.catatan = '';
            this.showModal = true;
        },
        openModal(id) {
            this.lastFocusedElement = document.activeElement;
            this.selectedSantri = id;
            this.selectedStatus = 'hadir';
            this.waktuTiba = '';
            this.catatan = '';
            this.currentSantriId = id;
            this.currentAction = 'add';
            this.showModal = true;
            this.$nextTick(() => {
                this.$refs.modalContent?.focus();
            });
        },
        editModal(id) {
            this.lastFocusedElement = document.activeElement;
            this.selectedSantri = id;
            this.currentSantriId = id;
            this.currentAction = 'edit';
            const existing = this.existingAbsensi || {};
            if (existing[id]) {
                this.selectedStatus = existing[id].status || 'hadir';
                this.waktuTiba = existing[id].waktu_tiba || '';
                this.catatan = existing[id].catatan || '';
            } else {
                this.selectedStatus = 'hadir';
                this.waktuTiba = '';
                this.catatan = '';
            }
            this.showModal = true;
            this.$nextTick(() => {
                this.$refs.modalContent?.focus();
            });
        },
        closeModal() {
            this.showModal = false;
            this.resetForm();
            // Return focus to trigger element
            this.$nextTick(() => {
                if (this.lastFocusedElement) {
                    this.lastFocusedElement.focus();
                }
            });
        },
        resetForm() {
            this.selectedSantri = '';
            this.selectedStatus = '';
            this.waktuTiba = '';
            this.catatan = '';
            this.currentSantriId = null;
        },
        async savePresensi() {
            if (!this.selectedSantri) { alert('Pilih santri'); return; }
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'POST');
            formData.append('tanggal', '{{ $date }}');
            formData.append('sesi', '{{ $sesi }}');
            formData.append('absensi[' + this.selectedSantri + '][santri_id]', this.selectedSantri);
            formData.append('absensi[' + this.selectedSantri + '][status]', this.selectedStatus);
            formData.append('absensi[' + this.selectedSantri + '][waktu_tiba]', this.waktuTiba);
            formData.append('absensi[' + this.selectedSantri + '][catatan]', this.catatan);

            try {
                const res = await fetch('{{ route('ustadz.absensis.store') }}', { method: 'POST', body: formData });
                const text = await res.text();
                if (res.ok) {
                    window.location.href = '{{ route('ustadz.absensis.index', ['tanggal' => $date, 'sesi' => $sesi]) }}';
                } else {
                    alert('Gagal menyimpan presensi');
                }
            } catch (e) {
                alert('Gagal menyimpan presensi');
            }
        }
    }
}
</script>
@endsection