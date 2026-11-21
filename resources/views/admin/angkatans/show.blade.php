@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ showDeleteModal: false, deleteItem: null, openDeleteModal(id, name) { this.deleteItem = { id, name }; this.showDeleteModal = true; }, closeDeleteModal() { this.showDeleteModal = false; this.deleteItem = null; } }">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" href="{{ route('admin.angkatans.index') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </x-button>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Detail Angkatan: {{ $angkatan->nama }}</h2>
                <p class="mt-1 text-sm text-neutral-500">Tahun Mulai: {{ $angkatan->tahun_mulai }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                <x-button variant="ghost" href="{{ route('admin.angkatans.edit', $angkatan) }}">Edit</x-button>
                <x-button variant="destructive" data-name="{{ $angkatan->nama }}" @click="openDeleteModal('{{ $angkatan->id }}', $event.currentTarget.dataset.name)">Hapus</x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Informasi Angkatan</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-neutral-500">Nama</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $angkatan->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Tahun Mulai</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $angkatan->tahun_mulai }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Status</dt>
                            <dd class="mt-1">
                                @if ($angkatan->status_aktif)
                                    <x-badge variant="success" size="sm">Aktif</x-badge>
                                @else
                                    <x-badge variant="error" size="sm">Tidak Aktif</x-badge>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Jumlah Kelompok</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $angkatan->kelompoks->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Jumlah Santri</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $angkatan->santris->count() }}</dd>
                        </div>
                    </dl>
                </x-card>

                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Kelompok ({{ $angkatan->kelompoks->count() }})</h3>
                    @if ($angkatan->kelompoks->isEmpty())
                        <p class="text-neutral-500 text-center py-4">Belum ada kelompok.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-neutral-50 border-b border-neutral-200">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama Kelompok</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Musyrif</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Santri</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @foreach ($angkatan->kelompoks as $kelompok)
                                        <tr class="hover:bg-neutral-50">
                                            <td class="px-4 py-2 text-sm font-medium text-neutral-900">{{ $kelompok->nama }}</td>
                                            <td class="px-4 py-2 text-sm text-neutral-600">{{ $kelompok->ustadz->user->name ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-neutral-600">{{ $kelompok->santris->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Santri</h3>
                    @if ($angkatan->santris->isEmpty())
                        <p class="text-neutral-500 text-center py-4">Belum ada santri.</p>
                    @else
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach ($angkatan->santris->take(10) as $santri)
                                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-50">
                                    <x-badge variant="success" size="xs">{{ $santri->status_aktif }}</x-badge>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-neutral-900 truncate">{{ $santri->nama }}</p>
                                        <p class="text-xs text-neutral-500">{{ $santri->nis }}</p>
                                    </div>
                                </div>
                            @endforeach
                            @if ($angkatan->santris->count() > 10)
                                <x-button variant="ghost" size="sm" class="w-full" href="{{ route('admin.santris.index') }}">Lihat semua {{ $angkatan->santris->count() }} santri</x-button>
                            @endif
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </div>

        @include('admin.partials.delete-modal', [
            'title' => 'Hapus Angkatan',
            'itemLabel' => 'angkatan',
            'action' => route('admin.angkatans.destroy', ':id'),
        ])
    </div>
@endsection