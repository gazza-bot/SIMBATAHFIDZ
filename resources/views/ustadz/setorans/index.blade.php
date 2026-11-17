@extends('layouts.app', ['title' => 'Setoran Hafalan', 'pageTitle' => 'Setoran Hafalan'])

@section('content')

    <div class="relative max-w-6xl mx-auto space-y-6" data-table-container x-data="{ showDeleteModal: false, deleteItem: null, openDeleteModal(id, name) { this.deleteItem = { id, name }; this.showDeleteModal = true; }, closeDeleteModal() { this.showDeleteModal = false; this.deleteItem = null; } }">
        @include('admin.partials.table-loading')
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Setoran Hafalan</h2>
                <p class="mt-1 text-sm text-neutral-500">Kelola setoran hafalan santri di kelompok Anda.</p>
            </div>
            <div class="flex gap-2">
                <x-button variant="outline" href="{{ route('ustadz.setorans.validasi') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Validasi
                </x-button>
                <x-button href="{{ route('ustadz.setorans.create') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Input Setoran
                </x-button>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <x-card padding="sm">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Total</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $stats['total'] }}</p>
            </x-card>
            <x-card padding="sm">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Menunggu</p>
                <p class="mt-2 text-2xl font-bold text-warning-600">{{ $stats['pending'] }}</p>
            </x-card>
            <x-card padding="sm">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Disetujui</p>
                <p class="mt-2 text-2xl font-bold text-success-600">{{ $stats['approved'] }}</p>
            </x-card>
            <x-card padding="sm">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Ditolak</p>
                <p class="mt-2 text-2xl font-bold text-error-600">{{ $stats['rejected'] }}</p>
            </x-card>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <x-input type="text" name="search" placeholder="Cari nama atau NIS santri..." value="{{ request('search') }}" />
                <div>
                    <label for="filter-status" class="block text-sm font-medium text-neutral-700 mb-1.5">Status</label>
                    <select id="filter-status" name="status" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label for="filter-jenis" class="block text-sm font-medium text-neutral-700 mb-1.5">Jenis</label>
                    <select id="filter-jenis" name="jenis" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard">
                        <option value="">Semua Jenis</option>
                        <option value="ziyadah" {{ request('jenis') == 'ziyadah' ? 'selected' : '' }}>Ziyadah</option>
                        <option value="murajaah" {{ request('jenis') == 'murajaah' ? 'selected' : '' }}>Murajaah</option>
                    </select>
                </div>
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
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Jenis</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nilai</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
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
                                <td class="px-4 py-3 text-sm text-neutral-600">
                                    <x-badge variant="{{ $setoran->jenis == 'ziyadah' ? 'info' : 'secondary' }}" size="sm">{{ ucfirst($setoran->jenis) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <x-badge
                                        variant="{{ $setoran->nilai == 'mumtaz' ? 'mumtaz' : ($setoran->nilai == 'jayyid_jiddan' ? 'jayyid_jiddan' : ($setoran->nilai == 'jayyid' ? 'jayyid' : 'error')) }}"
                                        size="sm"
                                    >{{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($setoran->status == 'pending')
                                        <x-badge variant="warning" size="sm" dot>Menunggu</x-badge>
                                    @elseif ($setoran->status == 'approved')
                                        <x-badge variant="success" size="sm" dot>Disetujui</x-badge>
                                    @else
                                        <x-badge variant="error" size="sm" dot>Ditolak</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.show', $setoran) }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </x-button>
                                        @if ($setoran->status == 'pending')
                                            <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.edit', $setoran) }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </x-button>
                                            <x-button variant="destructive" size="sm" data-name="{{ $setoran->santri->nama ?? 'Setoran' }} ({{ $setoran->surah->nama_latin ?? '-' }})" @click="openDeleteModal('{{ $setoran->id }}', $event.currentTarget.dataset.name)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </x-button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center">
                                    <x-card padding="8">
                                        <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="mt-3 text-neutral-500">Tidak ada data setoran.</p>
                                        <x-button variant="primary" class="mt-3" href="{{ route('ustadz.setorans.create') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Input Setoran
                                        </x-button>
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

    {{-- Delete Confirmation Modal --}}
    <div
        x-show="showDeleteModal" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-95"
        x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-setoran-title"
            @keydown.escape.window="showDeleteModal = false"
            x-ref="deleteModal"
        >
            <div class="fixed inset-0 z-0 bg-neutral-950/70 backdrop-blur-sm" @click="showDeleteModal = false" aria-hidden="true"></div>
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-level-4">
                <div class="flex items-start justify-between gap-4 p-5 border-b border-neutral-100">
                    <div>
                        <h2 id="delete-setoran-title" class="text-lg font-semibold text-neutral-900">Hapus Setoran</h2>
                        <p class="mt-1 text-sm text-neutral-500">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <button
                        type="button"
                        class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                        @click="showDeleteModal = false"
                        aria-label="Tutup modal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5">
                    <p class="text-neutral-700">Apakah Anda yakin ingin menghapus setoran <span x-text="deleteItem?.name" class="font-medium"></span>?</p>
                    <form method="POST" :action="'{{ route('ustadz.setorans.destroy', ':id') }}'.replace(':id', deleteItem?.id)">
                        @csrf
                        @method('DELETE')
                        <div class="mt-6 flex justify-end gap-2">
                            <x-button variant="ghost" @click="showDeleteModal = false">Batal</x-button>
                            <x-button type="submit" variant="destructive">Hapus</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection