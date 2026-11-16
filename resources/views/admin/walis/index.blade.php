@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    @include('admin.partials.master-tabs')

    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-neutral-900">{{$pageTitle}}</h2>
                <p class="mt-1 text-sm text-neutral-500">Kelola data wali santri.</p>
            </div>
            <x-button href="{{ route('admin.walis.create') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Wali
            </x-button>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
            <div class="mb-4">
                <x-input type="text" name="search" placeholder="Cari nama, username, atau nomor telepon..." value="{{ $filters['search'] ?? '' }}" />
            </div>

            <div class="overflow-x-auto rounded-2xl border border-neutral-200">
                <table class="w-full" role="grid">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama Wali</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">No. Telepon</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Alamat</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Jumlah Anak</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($walis as $wali)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-neutral-500">{{ $walis->firstItem() + $walis->currentPage() - 1 }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-neutral-600">{{ $wali->user->username }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $wali->nama }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $wali->telepon ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600 max-w-xs truncate">{{ $wali->alamat ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $wali->santris->count() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <x-button variant="ghost" size="sm" href="{{ route('admin.walis.show', $wali) }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Detail
                                        </x-button>
                                        <x-button variant="ghost" size="sm" href="{{ route('admin.walis.edit', $wali) }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </x-button>
                                        <x-button variant="destructive" size="sm" onclick="openDeleteWaliModal('{{ $wali->id }}', '{{ $wali->nama }}')">
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
                                        <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <p class="mt-3 text-neutral-500">Tidak ada data wali.</p>
                                        <x-button variant="primary" class="mt-3" href="{{ route('admin.walis.create') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Tambah Wali
                                        </x-button>
                                    </x-card>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($walis->hasPages())
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-neutral-500">
                        Menampilkan {{ $walis->firstItem() }} sampai {{ $walis->lastItem() }} dari {{ $walis->total() }} data
                    </div>
                    <div class="flex gap-1">
                        {{ $walis->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <x-modal id="delete-wali" title="Hapus Wali" description="Tindakan ini tidak dapat dibatalkan.">
        <p class="text-neutral-700">Apakah Anda yakin ingin menghapus wali <span id="delete-wali-name" class="font-medium"></span>?</p>
        <input type="hidden" id="delete-wali-id">
        <div class="mt-6 flex justify-end gap-2">
            <x-button variant="ghost" onclick="closeDeleteWaliModal()">Batal</x-button>
            <form method="POST" action="{{ route('admin.walis.destroy', ':id') }}" id="delete-wali-form">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="destructive">Hapus</x-button>
            </form>
        </div>
    </x-modal>

    @push('scripts')
    <script>
        function openDeleteWaliModal(id, name) {
            document.getElementById('delete-wali-id').value = id;
            document.getElementById('delete-wali-name').textContent = name;
            document.getElementById('delete-wali-form').action = '{{ route('admin.walis.destroy', ':id') }}'.replace(':id', id);
            document.getElementById('delete-wali').setAttribute('open', '');
        }
        function closeDeleteWaliModal() {
            document.getElementById('delete-wali').removeAttribute('open');
        }
    </script>
    @endpush

    {-- Delete Confirmation Modal --}
    <x-modal id="delete-walis" title="Hapus Wali" description="Tindakan ini tidak dapat dibatalkan.">
        <p class="text-neutral-700">Apakah Anda yakin ingin menghapus wali <span id="delete-walis-name" class="font-medium"></span>?</p>
        <input type="hidden" id="delete-walis-id">
        <div class="mt-6 flex justify-end gap-2">
            <x-button variant="ghost" onclick="closeDeleteWaliModal()">Batal</x-button>
            <form method="POST" action="{ route('admin.walis.destroy', ':id') }" id="delete-walis-form">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="destructive">Hapus</x-button>
            </form>
        </div>
    </x-modal>

    @push('scripts')
    <script>
        function openDeleteWaliModal(id, name) {
            document.getElementById('delete-walis-id').value = id;
            document.getElementById('delete-walis-name').textContent = name;
            document.getElementById('delete-walis-form').action = '{ route('admin.walis.destroy', ':id') }'.replace(':id', id);
            document.getElementById('delete-walis').setAttribute('open', '');
        }
        function closeDeleteWaliModal() {
            document.getElementById('delete-walis').removeAttribute('open');
        }
    </script>
    @endpush
@endsection