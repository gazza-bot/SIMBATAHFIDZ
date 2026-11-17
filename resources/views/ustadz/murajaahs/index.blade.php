@extends('layouts.app', ['title' => 'Murajaah Bulanan', 'pageTitle' => 'Murajaah Bulanan'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-neutral-900">Murajaah Bulanan</h2>
            <p class="text-sm text-neutral-500">Halaqah {{ \App\Models\Kelompok::where('ustadz_id', auth()->user()->ustadz->id)->first()?->nama ?? 'A' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Tertunda</p>
            <p class="mt-2 text-2xl font-bold text-error-600">{{ collect($santriData)->where('priority', 'high')->count() }}</p>
        </x-card>
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Tuntas</p>
            <p class="mt-2 text-2xl font-bold text-success-600">{{ collect($santriData)->where('priority', 'low')->count() }} / {{ count($santriData) }}</p>
        </x-card>
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Batas</p>
            <p class="mt-2 text-2xl font-bold text-warning-600">5 Hari Lagi</p>
        </x-card>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <select name="filter" class="w-full sm:w-48 rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                <option value="semua" {{ $filter == 'semua' ? 'selected' : '' }}>Semua</option>
                <option value="prioritas" {{ $filter == 'prioritas' ? 'selected' : '' }}>Prioritas >3 Minggu</option>
                <option value="3minggu" {{ $filter == '3minggu' ? 'selected' : '' }}>Belum >3 Minggu</option>
            </select>
            <x-button variant="primary" type="button" onclick="openBulkModal()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kirim Pengingat Masal
            </x-button>
        </div>

        <div class="space-y-3">
            @forelse ($santriData as $item)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-neutral-50 rounded-xl border border-neutral-100">
                    <div class="flex items-center gap-4">
                        <x-avatar name="{{ $item['santri']->nama }}" size="md" />
                        <div>
                            <p class="text-lg font-semibold text-neutral-900">{{ $item['santri']->nama }}</p>
                            <div class="flex items-center gap-3 text-sm text-neutral-500">
                                <span>{{ $item['santri']->nis }}</span>
                                <span>{{ $item['santri']->angkatan->nama ?? '-' }}</span>
                                <span>{{ $item['santri']->kelompok->nama ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        @if ($item['last_murajaah'])
                            <div>
                                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Terakhir Murajaah</p>
                                <p class="text-sm font-medium text-neutral-900">{{ $item['last_murajaah']->tanggal->translatedFormat('d F Y') }}</p>
                                <p class="text-xs text-neutral-500">{{ $item['days_since'] }} hari lalu</p>
                            </div>
                        @else
                            <div>
                                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Terakhir Murajaah</p>
                                <p class="text-sm font-medium text-error-600">Belum pernah</p>
                            </div>
                        @endif
                        <x-badge variant="{{ $item['priority'] == 'high' ? 'error' : ($item['priority'] == 'medium' ? 'warning' : 'success') }}" size="sm" dot>
                            {{ $item['priority'] == 'high' ? 'Prioritas' : ($item['priority'] == 'medium' ? 'Perlu Perhatian' : 'Tuntas') }}
                        </x-badge>
                        <x-button variant="ghost" size="sm" @click="openScheduleModal({{ $item['santri']->id }})">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Jadwalkan Sima'an
                        </x-button>
                    </div>
                </div>
            @empty
                <p class="text-center text-neutral-500 py-8">Tidak ada data santri.</p>
            @endforelse
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <x-button variant="secondary" onclick="downloadPdf()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Unduh Rekap Laporan Bulanan (PDF)
        </x-button>
    </div>
</div>

{{-- Schedule Modal --}}
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data="scheduleModal()" x-show="showModal" x-cloak>
    <div class="fixed inset-0 bg-neutral-950/70 backdrop-blur-sm" @click="closeModal" aria-hidden="true"></div>
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-level-4 p-6" role="dialog" aria-modal="true">
        <h3 class="text-lg font-semibold text-neutral-900 mb-4">Jadwalkan Sima'an</h3>
        <form method="POST" action="{{ route('ustadz.murajaahs.store') }}" @submit.prevent="submitForm">
            @csrf
            <input type="hidden" name="santri_id" :value="selectedSantriId">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1.5">Santri</label>
                    <select name="santri_id" :value="selectedSantriId" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2" readonly>
                        <option value="">Pilih Santri</option>
                        @foreach ($santriData as $item)
                            <option value="{{ $item['santri']->id }}">{{ $item['santri']->nama }} ({{ $item['santri']->nis }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-neutral-700 mb-1.5">Tanggal Sima'an</label>
                    <input type="date" name="tanggal" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2" required>
                </div>
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-neutral-700 mb-1.5">Keterangan</label>
                    <textarea name="keterangan" rows="3" placeholder="Catatan untuk sima'an..." class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="ghost" @click="closeModal">Batal</x-button>
                <x-button type="submit" variant="primary">Jadwalkan</x-button>
            </div>
        </form>
    </div>
</div>

<script>
function scheduleModal() {
    return {
        showModal: false,
        selectedSantriId: '',
        init() {
            this.$watch('showModal', (val) => {
                if (!val) this.selectedSantriId = '';
            });
        },
        openModal(santriId) {
            this.selectedSantriId = santriId;
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.selectedSantriId = '';
        },
        submitForm() {
            this.$refs.form.submit();
        }
    }
}
</script>
@endsection