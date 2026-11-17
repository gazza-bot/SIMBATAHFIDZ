@extends('layouts.app', ['title' => 'Validasi Setoran', 'pageTitle' => 'Validasi Setoran'])

@section('content')

    <div class="relative max-w-6xl mx-auto space-y-6" x-data="{ rejectModal: false, rejectSetoranId: null, rejectSetoranName: null, openRejectModal(id, name) { this.rejectSetoranId = id; this.rejectSetoranName = name; this.rejectModal = true; }, closeRejectModal() { this.rejectModal = false; this.rejectSetoranId = null; this.rejectSetoranName = null; } }">
        @include('admin.partials.table-loading')
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Validasi Setoran</h2>
                <p class="mt-1 text-sm text-neutral-500">Setujui atau tolak setoran hafalan santri.</p>
            </div>
            <x-button variant="ghost" href="{{ route('ustadz.setorans.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar
            </x-button>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
            <div class="mb-4">
                <x-input type="text" name="search" placeholder="Cari nama atau NIS santri..." value="{{ request('search') }}" />
            </div>

            <div class="overflow-x-auto rounded-2xl border border-neutral-200">
                <table class="w-full" role="grid">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Santri</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Surah</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Ayat</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nilai</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Ustadz</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($setorans as $setoran)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-neutral-500">{{ $setorans->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-neutral-900">
                                    {{ $setoran->santri->nama ?? '-' }}
                                    <span class="block text-xs text-neutral-500">{{ $setoran->santri->nis ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $setoran->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">
                                    {{ $setoran->surah->nama_latin ?? '-' }}
                                    <span class="block text-xs text-neutral-500">Juz {{ $setoran->juz }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-neutral-600">
                                    @if ($setoran->ayat_awal == $setoran->ayat_akhir)
                                        Ayat {{ $setoran->ayat_awal }}
                                    @else
                                        {{ $setoran->ayat_awal }}-{{ $setoran->ayat_akhir }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <x-badge
                                        variant="{{ $setoran->nilai == 'mumtaz' ? 'mumtaz' : ($setoran->nilai == 'jayyid_jiddan' ? 'jayyid_jiddan' : ($setoran->nilai == 'jayyid' ? 'jayyid' : 'error')) }}"
                                        size="sm"
                                    >{{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-sm text-neutral-600">
                                    {{ $setoran->ustadz->user->name ?? $setoran->ustadz->nama ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.show', $setoran) }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </x-button>
                                        <form method="POST" action="{{ route('ustadz.setorans.approve', $setoran) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <x-button type="submit" variant="ghost" size="sm" class="!text-success-600 hover:!bg-success-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Setujui
                                            </x-button>
                                        </form>
                                        <x-button variant="ghost" size="sm" class="!text-error-600 hover:!bg-error-50" @click="openRejectModal('{{ $setoran->id }}', '{{ $setoran->santri->nama ?? 'Santri' }} - {{ $setoran->surah->nama_latin ?? '-' }}')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Tolak
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center">
                                    <x-card padding="8">
                                        <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="mt-3 text-neutral-500">Tidak ada setoran yang menunggu validasi.</p>
                                    </x-card>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($setorans->hasPages())
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-neutral-500">
                        Menampilkan {{ $setorans->firstItem() }} sampai {{ $setorans->lastItem() }} dari {{ $setorans->total() }} data
                    </div>
                    <div class="flex gap-1">
                        {{ $setorans->links() }}
                    </div>
                </div>
            @endif
        </div>

    {{-- Reject Modal --}}
    <div
        x-show="rejectModal" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-95"
        x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="reject-setoran-title"
            @keydown.escape.window="rejectModal = false"
            x-ref="rejectModal"
        >
            <div class="fixed inset-0 z-0 bg-neutral-950/70 backdrop-blur-sm" @click="rejectModal = false" aria-hidden="true"></div>
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-level-4">
                <div class="flex items-start justify-between gap-4 p-5 border-b border-neutral-100">
                    <div>
                        <h2 id="reject-setoran-title" class="text-lg font-semibold text-neutral-900">Tolak Setoran</h2>
                        <p class="mt-1 text-sm text-neutral-500">Berikan alasan penolakan setoran.</p>
                    </div>
                    <button
                        type="button"
                        class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                        @click="rejectModal = false"
                        aria-label="Tutup modal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5">
                    <p class="text-neutral-700 mb-4">Setoran <span x-text="rejectSetoranName" class="font-medium"></span></p>
                    <form method="POST" :action="'{{ route('ustadz.setorans.reject', ':id') }}'.replace(':id', rejectSetoranId)">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="catatan_reject" class="block text-sm font-medium text-neutral-700 mb-1.5">
                                Alasan Penolakan
                                <span class="text-error-500 ml-1" aria-hidden="true">*</span>
                            </label>
                            <textarea
                                id="catatan_reject"
                                name="catatan_reject"
                                rows="3"
                                required
                                placeholder="Tuliskan alasan penolakan setoran ini..."
                                class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-error-500 focus:ring-error-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard"
                            ></textarea>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <x-button variant="ghost" @click="rejectModal = false">Batal</x-button>
                            <x-button type="submit" variant="destructive">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak Setoran
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection