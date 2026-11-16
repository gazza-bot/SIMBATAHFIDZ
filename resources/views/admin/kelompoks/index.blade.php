@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    @include('admin.partials.master-tabs')

    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-neutral-900">{{$pageTitle}}</h2>
                <p class="mt-1 text-sm text-neutral-500">Kelola data kelompok pesantren.</p>
            </div>
            <div class="flex gap-2">
                <x-button variant="secondary" onclick="toggleSidebar()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Sidebar
                </x-button>
                <x-button href="{{ route('admin.kelompoks.create') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kelompok
                </x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-card padding="sm">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Total Santri</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $kelompoks->total() }}</p>
            </x-card>
            <x-card padding="sm">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Aktif</p>
                <p class="mt-2 text-2xl font-bold text-success-600">{{ $kelompoks->where('status_aktif','aktif')->count() }}</p>
            </x-card>
            <x-card padding="sm">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Nonaktif</p>
                <p class="mt-2 text-2xl font-bold text-error-600">{{ $kelompoks->where('status_aktif','nonaktif')->count() }}</p>
            </x-card>
            <x-card padding="sm">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Lulus</p>
                <p class="mt-2 text-2xl font-bold text-secondary-600">{{ $kelompoks->where('status_aktif','lulus')->count() }}</p>
            </x-card>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <x-input type="text" name="search" placeholder="Cari nama kelompok..." value="{{ $filters['search'] ?? '' }}" />
                <x-input type="select" name="ustadz_id" placeholder="Semua Musyrif" value="{{ $filters['ustadz_id'] ?? '' }}">
                    <option value="">Semua Musyrif</option>
                    @foreach ($ustadzs as $ustadz)
                        <option value="{{ $ustadz->id }}" {{ $filters['ustadz_id'] ?? '' == $ustadz->id ? 'selected' : '' }}>
                            {{ $ustadz->nama }} ({{ $ustadz->user->username }})
                        </option>
                    @endforeach
                </x-input>
                <x-input type="select" name="angkatan_id" placeholder="Semua Angkatan" value="{{ $filters['search'] ?? '' }}">
                    <option value="">Semua Angkatan</option>
                    @foreach ($angkatans as $angkatan)
                        <option value="{{ $angkatan->id }}" {{ $filters['search'] ?? '' == $angkatan->id ? 'selected' : '' }}>
                            {{ $angkatan->nama }}
                        </option>
                    @endforeach
                </x-input>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-neutral-200">
                <table class="w-full" role="grid">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">NIS</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Angkatan</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Kelompok</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Wali</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($kelompoks as $kelompok)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-neutral-500">{{ $kelompoks->firstItem() + $kelompoks->currentPage() - 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $kelompok->nis }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $kelompok->nama }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $kelompok->angkatan->nama ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $kelompok->kelompok->nama ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $kelompok->wali->user->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <x-badge variant="success" size="sm">{{ $kelompok->status_aktif }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <x-button variant="ghost" size="sm" href="{{ route('admin.kelompoks.show', $kelompok) }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </x-button>
                                        <x-button variant="ghost" size="sm" href="{{ route('admin.kelompoks.edit', $kelompok) }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </x-button>
                                        <x-button variant="destructive" size="sm" x-data @click="deleteKelompokItem = { id: '{{ $kelompok->id }}', name: '{{ $kelompok->nama }}' }; showDeleteKelompokModal = true">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center">
                                    <x-card padding="none">
                                        <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <p class="mt-3 text-neutral-500">Tidak ada data santri.</p>
                                        <x-button variant="primary" class="mt-3" href="{{ route('admin.kelompoks.create') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Tambah Kelompok
                                        </x-button>
                                    </x-card>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($kelompoks->hasPages())
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-neutral-500">
                        Menampilkan {{ $kelompoks->firstItem() }} sampai {{ $kelompoks->lastItem() }} dari {{ $kelompoks->total() }} data
                    </div>
                    <div class="flex gap-1">
                        {{ $kelompoks->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal (Alpine.js) --}}
    <div x-data="{ showDeleteKelompokModal: false, deleteKelompokItem: null }" x-init="deleteKelompokModal = $refs.deleteKelompokModal">
        <div
            x-show="showDeleteKelompokModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-kelompok-title"
            @keydown.escape.window="showDeleteKelompokModal = false"
            x-ref="deleteKelompokModal"
        >
            <div class="fixed inset-0 bg-neutral-950/70 backdrop-blur-sm" @click="showDeleteKelompokModal = false" aria-hidden="true"></div>
            <div class="w-full max-w-md bg-white rounded-2xl shadow-level-4">
                <div class="flex items-start justify-between gap-4 p-5 border-b border-neutral-100">
                    <div>
                        <h2 id="delete-kelompok-title" class="text-lg font-semibold text-neutral-900">Hapus Kelompok</h2>
                        <p class="mt-1 text-sm text-neutral-500">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <button
                        type="button"
                        class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                        @click="showDeleteKelompokModal = false"
                        aria-label="Tutup modal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5">
                    <p class="text-neutral-700">Apakah Anda yakin ingin menghapus kelompok <span x-text="deleteKelompokItem?.name" class="font-medium"></span>?</p>
                    <form method="POST" :action="'{{ route('admin.kelompoks.destroy', ':id') }}'.replace(':id', deleteKelompokItem?.id)">
                        @csrf
                        @method('DELETE')
                        <div class="mt-6 flex justify-end gap-2">
                            <x-button variant="ghost" @click="showDeleteKelompokModal = false">Batal</x-button>
                            <x-button type="submit" variant="destructive">Hapus</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('deleteKelompok', () => ({
                showDeleteKelompokModal: false,
                deleteKelompokItem: null,
                openDeleteKelompokModal(id, name) {
                    this.deleteKelompokItem = { id, name };
                    this.showDeleteKelompokModal = true;
                },
                closeDeleteKelompokModal() {
                    this.showDeleteKelompokModal = false;
                    this.deleteKelompokItem = null;
                }
            }));
        });
    </script>
    @endpush
@endsection