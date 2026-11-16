@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    @include('admin.partials.master-tabs')

    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-neutral-900">{{$pageTitle}}</h2>
                <p class="mt-1 text-sm text-neutral-500">Kelola data ustadz musyrif.</p>
            </div>
            <x-button href="{{ route('admin.ustadzs.create') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Ustadz
            </x-button>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
            <div class="mb-4">
                <x-input type="text" name="search" placeholder="Cari NIP, nama, atau username..." value="{{ $filters['search'] ?? '' }}" />
            </div>

            <div class="overflow-x-auto rounded-2xl border border-neutral-200">
                <table class="w-full" role="grid">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">NIP</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama Ustadz</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">No. Telepon</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Kelompok Dibimbing</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($ustadzs as $ustadz)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-neutral-500">{{ $ustadzs->firstItem() + $ustadzs->currentPage() - 1 }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-neutral-600">{{ $ustadz->nip }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-neutral-600">{{ $ustadz->user->username }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $ustadz->nama }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $ustadz->telepon ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">
                                    @if ($ustadz->kelompoks->isNotEmpty())
                                        @foreach ($ustadz->kelompoks as $kelompok)
                                            <span class="inline-block px-2 py-0.5 rounded text-xs bg-primary-50 text-primary-600 mr-1 mb-1">{{ $kelompok->nama }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <x-button variant="ghost" size="sm" onclick="window.location.href='{{ route('admin.ustadzs.show', $ustadz) }}'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Detail
                                        </x-button>
                                        <x-button variant="ghost" size="sm" onclick="window.location.href='{{ route('admin.ustadzs.edit', $ustadz) }}'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </x-button>
                                        <x-button variant="destructive" size="sm" wire:click="confirmDelete('{{ $ustadz->id }}')">
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
                                        <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <p class="mt-3 text-neutral-500">Tidak ada data ustadz.</p>
                                        <x-button variant="primary" class="mt-3" href="{{ route('admin.ustadzs.create') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Tambah Ustadz
                                        </x-button>
                                    </x-card>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($ustadzs->hasPages())
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-neutral-500">
                        Menampilkan {{ $ustadzs->firstItem() }} sampai {{ $ustadzs->lastItem() }} dari {{ $ustadzs->total() }} data
                    </div>
                    <div class="flex gap-1">
                        {{ $ustadzs->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {-- Delete Confirmation Modal --}
    <x-modal id="delete-ustadzs" title="Hapus Ustadz" description="Tindakan ini tidak dapat dibatalkan.">
        <p class="text-neutral-700">Apakah Anda yakin ingin menghapus ustadz <span id="delete-ustadzs-name" class="font-medium"></span>?</p>
        <input type="hidden" id="delete-ustadzs-id">
        <div class="mt-6 flex justify-end gap-2">
            <x-button variant="ghost" onclick="closeDeleteUstadzModal()">Batal</x-button>
            <form method="POST" action="{ route('admin.ustadzs.destroy', ':id') }" id="delete-ustadzs-form">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="destructive">Hapus</x-button>
            </form>
        </div>
    </x-modal>

    @push('scripts')
    <script>
        function openDeleteUstadzModal(id, name) {
            document.getElementById('delete-ustadzs-id').value = id;
            document.getElementById('delete-ustadzs-name').textContent = name;
            document.getElementById('delete-ustadzs-form').action = '{ route('admin.ustadzs.destroy', ':id') }'.replace(':id', id);
            document.getElementById('delete-ustadzs').setAttribute('open', '');
        }
        function closeDeleteUstadzModal() {
            document.getElementById('delete-ustadzs').removeAttribute('open');
        }
    </script>
    @endpush
@endsection