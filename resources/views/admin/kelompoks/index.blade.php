@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    @include('admin.partials.master-tabs')

    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-neutral-900">{{$pageTitle}}</h2>
                <p class="mt-1 text-sm text-neutral-500">Kelola data kelompok halaqah.</p>
            </div>
            <x-button href="{{ route('admin.kelompoks.create') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kelompok
            </x-button>
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
                <x-input type="select" name="angkatan_id" placeholder="Semua Angkatan" value="{{ $filters['angkatan_id'] ?? '' }}">
                    <option value="">Semua Angkatan</option>
                    @foreach ($angkatans as $angkatan)
                        <option value="{{ $angkatan->id }}" {{ $filters['angkatan_id'] ?? '' == $angkatan->id ? 'selected' : '' }}>
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
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama Kelompok</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Musyrif</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Angkatan</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Jumlah Santri</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($kelompoks as $kelompok)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-neutral-500">{{ $kelompoks->firstItem() + $kelompoks->currentPage() - 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $kelompok->nama }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $kelompok->ustadz->user->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $kelompok->angkatan->nama ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $kelompok->santris->count() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <x-button variant="ghost" size="sm" onclick="window.location.href='{{ route('admin.kelompoks.show', $kelompok) }}'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Detail
                                        </x-button>
                                        <x-button variant="ghost" size="sm" onclick="window.location.href='{{ route('admin.kelompoks.edit', $kelompok) }}'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </x-button>
                                        <x-button variant="destructive" size="sm" wire:click="confirmDelete('{{ $kelompok->id }}')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center">
                                    <x-card padding="none">
                                        <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <p class="mt-3 text-neutral-500">Tidak ada data kelompok.</p>
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

    {-- Delete Confirmation Modal --}
    <x-modal id="delete-kelompoks" title="Hapus Kelompok" description="Tindakan ini tidak dapat dibatalkan.">
        <p class="text-neutral-700">Apakah Anda yakin ingin menghapus kelompok <span id="delete-kelompoks-name" class="font-medium"></span>?</p>
        <input type="hidden" id="delete-kelompoks-id">
        <div class="mt-6 flex justify-end gap-2">
            <x-button variant="ghost" onclick="closeDeleteKelompokModal()">Batal</x-button>
            <form method="POST" action="{ route('admin.kelompoks.destroy', ':id') }" id="delete-kelompoks-form">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="destructive">Hapus</x-button>
            </form>
        </div>
    </x-modal>

    @push('scripts')
    <script>
        function openDeleteKelompokModal(id, name) {
            document.getElementById('delete-kelompoks-id').value = id;
            document.getElementById('delete-kelompoks-name').textContent = name;
            document.getElementById('delete-kelompoks-form').action = '{ route('admin.kelompoks.destroy', ':id') }'.replace(':id', id);
            document.getElementById('delete-kelompoks').setAttribute('open', '');
        }
        function closeDeleteKelompokModal() {
            document.getElementById('delete-kelompoks').removeAttribute('open');
        }
    </script>
    @endpush
@endsection