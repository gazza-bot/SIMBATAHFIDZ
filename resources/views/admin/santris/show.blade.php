@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ showDeleteModal: false, deleteItem: null, openDeleteModal(id, name) { this.deleteItem = { id, name }; this.showDeleteModal = true; }, closeDeleteModal() { this.showDeleteModal = false; this.deleteItem = null; } }">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" href="{{ route('admin.santris.index') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </x-button>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Detail Santri: {{ $santri->nama }}</h2>
                <p class="mt-1 text-sm text-neutral-500">NIS: {{ $santri->nis }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                <x-button variant="ghost" href="{{ route('admin.santris.edit', $santri) }}">Edit</x-button>
                <x-button variant="destructive" data-name="{{ $santri->nama }}" @click="openDeleteModal('{{ $santri->id }}', $event.currentTarget.dataset.name)">Hapus</x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Informasi Dasar</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-neutral-500">NIS</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $santri->nis }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Nama Lengkap</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $santri->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Angkatan</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $santri->angkatan->nama ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Kelompok Halaqah</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $santri->kelompok->nama ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Musyrif</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $santri->kelompok->ustadz->user->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Wali Santri</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $santri->wali->user->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Status</dt>
                            <dd class="mt-1">
                                <x-badge variant="success" size="sm">{{ $santri->status_aktif }}</x-badge>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Tanggal Masuk</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $santri->tanggal_masuk?->format('d M Y') ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-card>

                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Progres Hafalan</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm text-neutral-500">Juz Terakhir Mutqin</dt>
                            <dd class="mt-1 text-xl font-bold text-primary-600">{{ $santri->progress_juz ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Persentase Capaian</dt>
                            <dd class="mt-1 text-xl font-bold text-success-600">{{ $santri->progress_percent }}%</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Mutu Terakhir</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $santri->mutu_terakhir ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-card>

                <x-card padding="default">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">Peta Hafalan 30 Juz</h3>
                        <span class="text-xs text-neutral-500">Cakupan ayat per juz (ziyadah approved)</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-success-50 border border-success-200">
                            <p class="text-xs text-success-700">Mutqin (100%)</p>
                            <p class="text-xl font-bold text-success-600">{{ $juzCounts['mutqin'] }} Juz</p>
                        </div>
                        <div class="p-3 rounded-xl bg-accent-50 border border-accent-200">
                            <p class="text-xs text-accent-700">Parsial (1-99%)</p>
                            <p class="text-xl font-bold text-accent-600">{{ $juzCounts['parsial'] }} Juz</p>
                        </div>
                        <div class="p-3 rounded-xl bg-neutral-100 border border-neutral-200">
                            <p class="text-xs text-neutral-600">Belum mulai</p>
                            <p class="text-xl font-bold text-neutral-600">{{ $juzCounts['belum'] }} Juz</p>
                        </div>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-white p-3">
<x-peta-juz
    :juzStatus="$juzStatus"
    :percentages="$juzPercentages"
    variant="status"
    columns="3"
    clickable="false"
/>
                    </div>
                </x-card>

                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Riwayat Setoran Terbaru</h3>
                    @if ($santri->setorans->isEmpty())
                        <p class="text-neutral-500 text-center py-4">Belum ada riwayat setoran.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-neutral-50 border-b border-neutral-200">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Surah</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Ayat</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Jenis</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Mutu</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @foreach ($santri->setorans->take(10) as $setoran)
                                        <tr class="hover:bg-neutral-50">
                                            <td class="px-4 py-2 text-sm text-neutral-600">{{ $setoran->tanggal?->format('d M Y') }}</td>
                                            <td class="px-4 py-2 text-sm text-neutral-900">{{ $setoran->surah->nama_arab ?? $setoran->surah->nama_latin }}</td>
                                            <td class="px-4 py-2 text-sm text-neutral-600">{{ $setoran->ayat_awal }}–{{ $setoran->ayat_akhir }}</td>
                                            <td class="px-4 py-2"><x-badge variant="info" size="xs">{{ $setoran->jenis }}</x-badge></td>
                                            <td class="px-4 py-2"><x-badge variant="secondary" size="xs">{{ $setoran->nilai }}</x-badge></td>
                                            <td class="px-4 py-2"><x-badge variant="success" size="xs">{{ $setoran->status }}</x-badge></td>
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
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Foto Santri</h3>
                    <div class="flex items-center justify-center">
                        <div class="h-32 w-32 rounded-xl overflow-hidden bg-neutral-100">
                            @if ($santri->foto)
                                <img src="{{ asset('storage/' . $santri->foto) }}" alt="Foto {{ $santri->nama }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-neutral-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-card>

                <x-card padding="default">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">Absensi Terbaru</h3>
                    @if ($santri->absensis->isEmpty())
                        <p class="text-neutral-500 text-center py-4">Belum ada data absensi.</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($santri->absensis->take(5) as $absensi)
                                <div class="flex items-center gap-3 p-2 rounded-lg bg-neutral-50">
                                    <x-badge variant="{{ $absensi->status === 'hadir' ? 'success' : ($absensi->status === 'izin' ? 'info' : ($absensi->status === 'sakit' ? 'warning' : 'error')) }}" size="sm">{{ $absensi->status }}</x-badge>
                                    <span class="text-sm text-neutral-600">{{ $absensi->tanggal?->format('d M Y') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
        @include('admin.partials.delete-modal', [
            'title' => 'Hapus Santri',
            'itemLabel' => 'santri',
            'action' => route('admin.santris.destroy', ':id'),
        ])
    </div>
@endsection