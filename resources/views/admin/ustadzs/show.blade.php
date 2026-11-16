@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ showDeleteModal: false, deleteItem: null, openDeleteModal(id, name) { this.deleteItem = { id, name }; this.showDeleteModal = true; }, closeDeleteModal() { this.showDeleteModal = false; this.deleteItem = null; } }">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" href="{{ route('admin.ustadzs.index') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </x-button>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Detail Ustadz: {{ $ustadz->nama }}</h2>
                <p class="mt-1 text-sm text-neutral-500">NIP: {{ $ustadz->nip }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                <x-button variant="ghost" href="{{ route('admin.ustadzs.edit', $ustadz) }}">Edit</x-button>
                <x-button variant="destructive" data-name="{{ $ustadz->nama }}" @click="openDeleteModal('{{ $ustadz->id }}', $event.currentTarget.dataset.name)">Hapus</x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Informasi Ustadz</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-neutral-500">Username</dt>
                            <dd class="mt-1 font-mono text-neutral-900">{{ $ustadz->user->username }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">NIP</dt>
                            <dd class="mt-1 font-mono text-neutral-900">{{ $ustadz->nip }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Nama Lengkap</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $ustadz->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">No. Telepon</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $ustadz->telepon ?? '—' }}</dd>
                        </div>
                    </dl>
                </x-card>

                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Kelompok Dibimbing ({{ $ustadz->kelompoks->count() }})</h3>
                    @if ($ustadz->kelompoks->isEmpty())
                        <p class="text-neutral-500 text-center py-4">Belum ada kelompok yang dibimbing.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-neutral-50 border-b border-neutral-200">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Kelompok</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Angkatan</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Jumlah Santri</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @foreach ($ustadz->kelompoks as $kelompok)
                                        <tr class="hover:bg-neutral-50">
                                            <td class="px-4 py-2 text-sm font-medium text-neutral-900">{{ $kelompok->nama }}</td>
                                            <td class="px-4 py-2 text-sm text-neutral-600">{{ $kelompok->angkatan->nama ?? '—' }}</td>
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
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Statistik</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-neutral-500">Kelompok Dibimbing</dt>
                            <dd class="mt-1 text-2xl font-bold text-primary-600">{{ $ustadz->kelompoks->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Total Santri</dt>
                            <dd class="mt-1 text-2xl font-bold text-success-600">{{ $ustadz->kelompoks->sum(fn($k) => $k->santris->count()) }}</dd>
                        </div>
                    </dl>
                </x-card>
            </div>
        </div>
    </div>

        @include('admin.partials.delete-modal', [
            'title' => 'Hapus Ustadz',
            'itemLabel' => 'ustadz',
            'action' => route('admin.ustadzs.destroy', ':id'),
        ])
    </div>
@endsection