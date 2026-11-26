@extends('layouts.app', ['title' => 'Murajaah Bulanan', 'pageTitle' => 'Murajaah Bulanan'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="scheduleModal()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-neutral-900">Murajaah Bulanan</h2>
            <p class="text-sm text-neutral-500">Halaqah {{ \App\Models\Kelompok::where('ustadz_id', auth()->user()->ustadz->id)->first()?->nama ?? 'A' }}</p>
        </div>
    </div>

    {{-- Filter Bulan --}}
    <form method="GET" class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="bulan" class="block text-xs font-medium text-neutral-700 mb-1.5">Bulan</label>
                <input type="month" name="bulan" id="bulan" value="{{ $bulan }}" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
            </div>
            <div class="sm:w-auto">
                <x-button type="submit" variant="primary" class="min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.293V4z"/></svg>
                    Tampilkan
                </x-button>
            </div>
        </div>
    </form>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Sudah Murajaah</p>
            <p class="mt-2 text-2xl font-bold text-success-600">{{ $sudahCount }}</p>
        </x-card>
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Belum Murajaah</p>
            <p class="mt-2 text-2xl font-bold text-error-600">{{ $belumCount }}</p>
        </x-card>
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Persentase</p>
            <p class="mt-2 text-2xl font-bold text-primary-600">{{ $persen }}%</p>
        </x-card>
    </div>

    {{-- Daftar Santri --}}
    <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
        <div class="space-y-3">
            @forelse ($santriData as $item)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-neutral-50 rounded-xl border border-neutral-100">
                    <div class="flex items-center gap-4 min-w-0">
                        <x-avatar name="{{ $item['santri']->nama }}" size="md" />
                        <div class="min-w-0">
                            <p class="text-lg font-semibold text-neutral-900 truncate">{{ $item['santri']->nama }}</p>
                            <div class="flex items-center gap-3 text-sm text-neutral-500">
                                <span>{{ $item['santri']->nis }}</span>
                                <span>{{ $item['santri']->angkatan->nama ?? '-' }}</span>
                                <span>{{ $item['santri']->kelompok->nama ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:flex-wrap">
                        @if ($item['last_murajaah'])
                            <div>
                                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Terakhir Murajaah</p>
                                <p class="text-sm font-medium text-neutral-900">{{ $item['last_murajaah']->tanggal->translatedFormat('d F Y') }}</p>
                            </div>
                        @else
                            <div>
                                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Terakhir Murajaah</p>
                                <p class="text-sm font-medium text-error-600">Belum pernah</p>
                            </div>
                        @endif

                        <x-badge variant="{{ $item['status'] === 'sudah' ? 'success' : 'error' }}" size="sm" dot>
                            {{ $item['status'] === 'sudah' ? 'Sudah' : 'Belum' }} ({{ $item['count_bulan'] }})
                        </x-badge>

                        @if ($item['status'] === 'belum')
                            <a href="{{ route('ustadz.setorans.create', ['santri' => $item['santri']->id, 'jenis' => 'murajaah']) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-primary-600 text-white hover:bg-primary-700 transition-colors min-h-[44px]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tandai Sudah
                            </a>
                        @endif

                        <x-button variant="ghost" size="sm" @click="openModal({{ $item['santri']->id }})">
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
<div class="fixed inset-0 z-50 flex items-center justify-center" x-show="showModal" x-cloak>
    <div class="fixed inset-0 bg-neutral-950/70 backdrop-blur-sm" @click="closeModal" aria-hidden="true"></div>
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-level-4 p-6" role="dialog" aria-modal="true" x-ref="form">
        <h3 class="text-lg font-semibold text-neutral-900 mb-4">Jadwalkan Sima'an</h3>
        <form method="POST" action="{{ route('ustadz.murajaahs.store') }}" @submit.prevent="submitForm" x-ref="form">
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
                    <textarea name="keterangan" rows="3" placeholder="Catatan untuk sima'an..." class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-500 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2"></textarea>
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
        },
        downloadPdf() {
            const url = '{{ route("ustadz.murajaahs.index") }}' + '?bulan={{ $bulan }}&pdf=1';
            window.open(url, '_blank');
        }
    }
}
</script>
@endsection