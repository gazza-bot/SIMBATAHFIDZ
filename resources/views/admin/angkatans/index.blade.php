@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    @include('admin.partials.master-tabs')

    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-neutral-900">{{$pageTitle}}</h2>
                <p class="mt-1 text-sm text-neutral-500">Kelola data angkatan kelas.</p>
            </div>
            <x-button href="{{ route('admin.angkatans.create') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Angkatan
            </x-button>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                <x-input type="text" name="search" placeholder="Cari nama atau tahun..." value="{{ $filters['search'] ?? '' }}" />
                <x-input type="select" name="status_aktif" placeholder="Semua Status" value="{{ $filters['status_aktif'] ?? '' }}">
                    <option value="">Semua Status</option>
                    <option value="1" {{ $filters['status_aktif'] ?? '' == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ $filters['status_aktif'] ?? '' == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </x-input>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-neutral-200">
                <table class="w-full" role="grid">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Tahun Mulai</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Kelompok</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Santri</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($angkatans as $angkatan)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-neutral-500">{{ $angkatans->firstItem() + $angkatans->currentPage() - 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $angkatan->nama }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $angkatan->tahun_mulai }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $angkatan->kelompoks_count }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $angkatan->santris_count }}</td>
                                <td class="px-4 py-3">
                                    @if ($angkatan->status_aktif)
                                        <x-badge variant="success" size="sm">Aktif</x-badge>
                                    @else
                                        <x-badge variant="error" size="sm">Tidak Aktif</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <x-button variant="ghost" size="sm" onclick="window.location.href='{{ route('admin.angkatans.show', $angkatan) }}'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Detail
                                        </x-button>
                                        <x-button variant="ghost" size="sm" onclick="window.location.href='{{ route('admin.angkatans.edit', $angkatan) }}'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </x-button>
                                        <x-button variant="destructive" size="sm" wire:click="confirmDelete('{{ $angkatan->id }}')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center">
                                    <x-card padding="none">
                                        <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <p class="mt-3 text-neutral-500">Tidak ada data angkatan.</p>
                                        <x-button variant="primary" class="mt-3" href="{{ route('admin.angkatans.create') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Tambah Angkatan
                                        </x-button>
                                    </x-card>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($angkatans->hasPages())
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-neutral-500">
                        Menampilkan {{ $angkatans->firstItem() }} sampai {{ $angkatans->lastItem() }} dari {{ $angkatans->total() }} data
                    </div>
                    <div class="flex gap-1">
                        {{ $angkatans->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {-- Delete Confirmation Modal --}
    <x-modal id="delete-angkatans" title="Hapus Angkatan" description="Tindakan ini tidak dapat dibatalkan.">
        <p class="text-neutral-700">Apakah Anda yakin ingin menghapus angkatan <span id="delete-angkatans-name" class="font-medium"></span>?</p>
        <input type="hidden" id="delete-angkatans-id">
        <div class="mt-6 flex justify-end gap-2">
            <x-button variant="ghost" onclick="closeDeleteAngkatanModal()">Batal</x-button>
            <form method="POST" action="{ route('admin.angkatans.destroy', ':id') }" id="delete-angkatans-form">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="destructive">Hapus</x-button>
            </form>
        </div>
    </x-modal>

    @push('scripts')
    <script>
        function openDeleteAngkatanModal(id, name) {
            document.getElementById('delete-angkatans-id').value = id;
            document.getElementById('delete-angkatans-name').textContent = name;
            document.getElementById('delete-angkatans-form').action = '{ route('admin.angkatans.destroy', ':id') }'.replace(':id', id);
            document.getElementById('delete-angkatans').setAttribute('open', '');
        }
        function closeDeleteAngkatanModal() {
            document.getElementById('delete-angkatans').removeAttribute('open');
        }
    </script>
    @endpush
@endsection