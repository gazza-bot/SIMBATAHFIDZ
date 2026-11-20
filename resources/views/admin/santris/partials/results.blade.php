<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <x-card padding="lg">
        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Total Santri</p>
        <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $summary['total'] }}</p>
    </x-card>
    <x-card padding="lg">
        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Aktif</p>
        <p class="mt-2 text-2xl font-bold text-success-600">{{ $summary['aktif'] }}</p>
    </x-card>
    <x-card padding="lg">
        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Nonaktif</p>
        <p class="mt-2 text-2xl font-bold text-error-600">{{ $summary['nonaktif'] }}</p>
    </x-card>
    <x-card padding="lg">
        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Lulus</p>
        <p class="mt-2 text-2xl font-bold text-secondary-600">{{ $summary['lulus'] }}</p>
    </x-card>
</div>

<div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
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
                @forelse ($santris as $santri)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-neutral-500">{{ ($santris->currentPage() - 1) * $santris->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $santri->nis }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $santri->nama }}</td>
                        <td class="px-4 py-3 text-sm text-neutral-600">{{ $santri->angkatan->nama ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-neutral-600">{{ $santri->kelompok->nama ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-neutral-600">{{ $santri->wali->user->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $status = $santri->status_aktif;
                                $variant = match($status) {
                                    'aktif' => 'success',
                                    'nonaktif' => 'error',
                                    'lulus' => 'secondary',
                                    'pindah' => 'warning',
                                    default => 'neutral',
                                };
                            @endphp
                            <x-badge variant="{{ $variant }}" size="sm">{{ $status }}</x-badge>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1.5">
                                <x-button variant="ghost" size="sm" href="{{ route('admin.santris.show', $santri) }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                </x-button>
                                <x-button variant="ghost" size="sm" href="{{ route('admin.santris.edit', $santri) }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </x-button>
                                <x-button variant="destructive" size="sm" data-delete-id="{{ $santri->id }}" data-delete-name="{{ $santri->nama }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center">
                            <x-card padding="8">
                                <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <p class="mt-3 text-neutral-500">Tidak ada data santri.</p>
                                <x-button variant="primary" class="mt-3" href="{{ route('admin.santris.create') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Tambah Santri
                                </x-button>
                            </x-card>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($santris->hasPages())
        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-neutral-500">
                Menampilkan {{ $santris->firstItem() }} sampai {{ $santris->lastItem() }} dari {{ $santris->total() }} data
            </div>
            <div class="flex gap-1">
                {{ $santris->links() }}
            </div>
        </div>
    @endif
</div>