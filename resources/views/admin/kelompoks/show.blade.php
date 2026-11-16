@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    @include('admin.partials.master-tabs')

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ showDeleteModal: false, deleteItem: null, openDeleteModal(id, name) { this.deleteItem = { id, name }; this.showDeleteModal = true; }, closeDeleteModal() { this.showDeleteModal = false; this.deleteItem = null; } }">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" href="{{ route('admin.kelompoks.index') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </x-button>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Detail Kelompok: {{ $kelompok->nama }}</h2>
                <p class="mt-1 text-sm text-neutral-500">Musyrif: {{ $kelompok->ustadz->user->name ?? '—' }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                <x-button variant="ghost" href="{{ route('admin.kelompoks.edit', $kelompok) }}">Edit</x-button>
                <x-button variant="destructive" data-name="{{ $kelompok->nama }}" @click="openDeleteModal('{{ $kelompok->id }}', $event.currentTarget.dataset.name)">Hapus</x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Informasi Kelompok</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-neutral-500">Nama</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $kelompok->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Musyrif</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $kelompok->ustadz->user->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Angkatan</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $kelompok->angkatan->nama ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Jumlah Santri</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $kelompok->santris->count() }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm text-neutral-500">Deskripsi</dt>
                            <dd class="mt-1 font-medium text-neutral-900 whitespace-pre-wrap">{{ $kelompok->deskripsi ?? '—' }}</dd>
                        </div>
                    </dl>
                </x-card>

                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Santri Anggota</h3>
                    @if ($kelompok->santris->isEmpty())
                        <p class="text-neutral-500 text-center py-4">Belum ada santri.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-neutral-50 border-b border-neutral-200">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">NIS</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama Santri</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Wali</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @foreach ($kelompok->santris as $santri)
                                        <tr class="hover:bg-neutral-50">
                                            <td class="px-4 py-2 text-sm font-mono text-neutral-600">{{ $santri->nis }}</td>
                                            <td class="px-4 py-2 text-sm font-medium text-neutral-900">{{ $santri->nama }}</td>
                                            <td class="px-4 py-2 text-sm text-neutral-600">{{ $santri->wali->user->name ?? '—' }}</td>
                                            <td class="px-4 py-2"><x-badge variant="success" size="xs">{{ $santri->status_aktif }}</x-badge></td>
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
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <x-button variant="outline" class="w-full justify-start gap-2" href="{{ route('admin.santris.index') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Kelola Data Santri
                        </x-button>
                    </div>
                </x-card>
            </div>
        </div>
    </div>

        @include('admin.partials.delete-modal', [
            'title' => 'Hapus Kelompok',
            'itemLabel' => 'kelompok',
            'action' => route('admin.kelompoks.destroy', ':id'),
        ])
    </div>
@endsection