@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ showDeleteModal: false, deleteItem: null, openDeleteModal(id, name) { this.deleteItem = { id, name }; this.showDeleteModal = true; }, closeDeleteModal() { this.showDeleteModal = false; this.deleteItem = null; } }">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" href="{{ route('admin.walis.index') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </x-button>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Detail Wali: {{ $wali->nama }}</h2>
                <p class="mt-1 text-sm text-neutral-500">Username: {{ $wali->user->username }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                <x-button variant="ghost" href="{{ route('admin.walis.edit', $wali) }}">Edit</x-button>
                <x-button variant="destructive" data-name="{{ $wali->nama }}" @click="openDeleteModal('{{ $wali->id }}', $event.currentTarget.dataset.name)">Hapus</x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Informasi Wali</h3>
                    <dl class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-neutral-500">Username</dt>
                                <dd class="mt-1 font-mono text-neutral-900">{{ $wali->user->username }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-neutral-500">Nama Lengkap</dt>
                                <dd class="mt-1 font-medium text-neutral-900">{{ $wali->nama }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-neutral-500">No. Telepon</dt>
                                <dd class="mt-1 font-medium text-neutral-900">{{ $wali->telepon ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-neutral-500">Alamat</dt>
                                <dd class="mt-1 font-medium text-neutral-900 whitespace-pre-wrap">{{ $wali->alamat ?? '—' }}</dd>
                            </div>
                        </div>
                    </dl>
                </x-card>

                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Anak yang Diwali ({{ $wali->santris->count() }})</h3>
                    @if ($wali->santris->isEmpty())
                        <p class="text-neutral-500 text-center py-4">Belum ada anak yang terdaftar.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-neutral-50 border-b border-neutral-200">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">NIS</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama Santri</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Angkatan</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Kelompok</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @foreach ($wali->santris as $santri)
                                        <tr class="hover:bg-neutral-50">
                                            <td class="px-4 py-2 text-sm font-mono text-neutral-600">{{ $santri->nis }}</td>
                                            <td class="px-4 py-2 text-sm font-medium text-neutral-900">{{ $santri->nama }}</td>
                                            <td class="px-4 py-2 text-sm text-neutral-600">{{ $santri->angkatan->nama ?? '—' }}</td>
                                            <td class="px-4 py-2 text-sm text-neutral-600">{{ $santri->kelompok->nama ?? '—' }}</td>
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
                        <x-button variant="outline" class="w-full justify-start gap-2" onclick="window.location.href='{{ route('wali.mutabaah') }}'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Lihat Dasbor Wali
                        </x-button>
                    </div>
                </x-card>
            </div>
        </div>
    </div>

        @include('admin.partials.delete-modal', [
            'title' => 'Hapus Wali',
            'itemLabel' => 'wali',
            'action' => route('admin.walis.destroy', ':id'),
        ])
    </div>
@endsection