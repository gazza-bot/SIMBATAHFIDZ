@extends('layouts.app', ['title' => 'Setoran Hafalan', 'pageTitle' => 'Setoran Hafalan'])

@section('content')
<div
    class="relative mx-auto max-w-6xl space-y-5"
    data-table-container
    x-data="{ showDeleteModal: false, deleteItem: null, openDeleteModal(id, name) { this.deleteItem = { id, name }; this.showDeleteModal = true; }, closeDeleteModal() { this.showDeleteModal = false; this.deleteItem = null; } }"
>
    @include('admin.partials.table-loading')

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Dasbor Halaqah', 'url' => route('ustadz.dashboard')],
                ['label' => 'Setoran Hafalan'],
            ]" />
            <h1 class="mt-2 text-xl font-bold text-neutral-900 sm:text-2xl">Setoran Hafalan</h1>
            <p class="mt-1 text-sm text-neutral-500">Riwayat setoran santri di kelompok Anda.</p>
        </div>
        <x-button href="{{ route('ustadz.setorans.create') }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Input Setoran
        </x-button>
    </div>

    <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-level-1 sm:p-8">
        <form method="GET" action="{{ route('ustadz.setorans.index') }}" class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_12rem_auto]">
            <div>
                <label for="search" class="mb-1.5 block text-sm font-medium text-neutral-700">Cari santri</label>
                <input
                    type="search"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nama atau NIS santri"
                    class="min-h-11 w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-500 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2"
                >
            </div>
            <div>
                <label for="filter-jenis" class="mb-1.5 block text-sm font-medium text-neutral-700">Jenis</label>
                <select
                    id="filter-jenis"
                    name="jenis"
                    onchange="this.form.submit()"
                    class="min-h-11 w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2"
                >
                    <option value="">Semua jenis</option>
                    <option value="ziyadah" @selected(request('jenis') === 'ziyadah')>Ziyadah</option>
                    <option value="murajaah" @selected(request('jenis') === 'murajaah')>Murajaah</option>
                    <option value="tasmi" @selected(request('jenis') === 'tasmi')>Tasmi'</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                    Terapkan
                </button>
                @if (request()->filled('search') || request()->filled('jenis'))
                    <a href="{{ route('ustadz.setorans.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg px-3 py-2 text-sm font-medium text-neutral-500 hover:bg-neutral-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="hidden overflow-x-auto rounded-xl border border-neutral-200 md:block">
            <table class="w-full">
                <thead class="border-b border-neutral-200 bg-neutral-50">
                    <tr>
                        <th scope="col" class="w-12 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-600">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-600">Santri</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-600">Tanggal</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-600">Bacaan</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-600">Jenis</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-600">Predikat</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-neutral-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($setorans as $setoran)
                        @php
                            $nilaiVariant = match ($setoran->nilai) {
                                'mumtaz' => 'mumtaz',
                                'jayyid_jiddan' => 'jayyid_jiddan',
                                'jayyid' => 'jayyid',
                                default => 'error',
                            };
                        @endphp
                        <tr class="transition-colors hover:bg-neutral-50">
                            <td class="px-4 py-3 text-sm text-neutral-500">{{ $setorans->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-neutral-900">{{ $setoran->santri->nama ?? '-' }}</p>
                                <p class="text-xs text-neutral-500">{{ $setoran->santri->nis ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $setoran->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-neutral-900">{{ $setoran->surah->nama_latin ?? '-' }}</p>
                                <p class="text-xs text-neutral-500">{{ $setoran->ayat_range }} | Juz {{ $setoran->juz }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <x-badge variant="{{ $setoran->jenis === 'ziyadah' ? 'info' : ($setoran->jenis === 'murajaah' ? 'secondary' : 'primary') }}" size="sm">
                                    {{ $setoran->jenis_label }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <x-badge :variant="$nilaiVariant" size="sm">{{ $setoran->nilai_label }}</x-badge>
                                @if ($setoran->is_cover_penuh)
                                    <p class="mt-1 text-[11px] font-medium text-success-700">Cover penuh</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.show', $setoran) }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </x-button>
                                    <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.edit', $setoran) }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </x-button>
                                    <x-button
                                        variant="destructive"
                                        size="sm"
                                        data-name="{{ $setoran->santri->nama ?? 'Setoran' }} ({{ $setoran->surah->nama_latin ?? '-' }})"
                                        @click="openDeleteModal('{{ $setoran->id }}', $event.currentTarget.dataset.name)"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="mt-3 text-sm text-neutral-500">Belum ada setoran yang cocok.</p>
                                <x-button variant="primary" size="sm" class="mt-3" href="{{ route('ustadz.setorans.create') }}">Input setoran</x-button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 md:hidden">
            @forelse ($setorans as $setoran)
                @php
                    $nilaiVariant = match ($setoran->nilai) {
                        'mumtaz' => 'mumtaz',
                        'jayyid_jiddan' => 'jayyid_jiddan',
                        'jayyid' => 'jayyid',
                        default => 'error',
                    };
                @endphp
                <article class="rounded-xl border border-neutral-200 p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-neutral-900">{{ $setoran->santri->nama ?? '-' }}</p>
                            <p class="text-xs text-neutral-500">{{ $setoran->santri->nis ?? '-' }} | {{ $setoran->tanggal->format('d/m/Y') }}</p>
                        </div>
                        <x-badge :variant="$nilaiVariant" size="sm">{{ $setoran->nilai_label }}</x-badge>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <x-badge variant="{{ $setoran->jenis === 'ziyadah' ? 'info' : ($setoran->jenis === 'murajaah' ? 'secondary' : 'primary') }}" size="sm">{{ $setoran->jenis_label }}</x-badge>
                        <span class="text-sm text-neutral-700">{{ $setoran->surah->nama_latin ?? '-' }}</span>
                        <span class="text-xs text-neutral-500">{{ $setoran->ayat_range }} | Juz {{ $setoran->juz }}</span>
                    </div>
                    @if ($setoran->is_cover_penuh)
                        <p class="mt-2 text-xs font-medium text-success-700">Cover penuh surah</p>
                    @endif
                    <div class="mt-3 flex flex-wrap gap-2">
                        <x-button variant="outline" size="sm" href="{{ route('ustadz.setorans.show', $setoran) }}">Detail</x-button>
                        <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.edit', $setoran) }}">Edit</x-button>
                        <x-button
                            variant="destructive"
                            size="sm"
                            data-name="{{ $setoran->santri->nama ?? 'Setoran' }} ({{ $setoran->surah->nama_latin ?? '-' }})"
                            @click="openDeleteModal('{{ $setoran->id }}', $event.currentTarget.dataset.name)"
                        >Hapus</x-button>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-neutral-200 p-8 text-center">
                    <p class="text-sm text-neutral-500">Belum ada setoran yang cocok.</p>
                    <x-button variant="primary" size="sm" class="mt-3" href="{{ route('ustadz.setorans.create') }}">Input setoran</x-button>
                </div>
            @endforelse
        </div>

        @if ($setorans->hasPages())
            <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-neutral-500">
                    Menampilkan {{ $setorans->firstItem() }} sampai {{ $setorans->lastItem() }} dari {{ $setorans->total() }} setoran
                </p>
                <div>{{ $setorans->links() }}</div>
            </div>
        @endif
    </div>

    <div
        x-show="showDeleteModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-95"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-setoran-title"
        @keydown.escape.window="showDeleteModal = false"
    >
        <div class="fixed inset-0 z-0 bg-neutral-950/70 backdrop-blur-sm" @click="showDeleteModal = false" aria-hidden="true"></div>
        <div class="relative z-10 w-full max-w-md rounded-2xl bg-white shadow-level-4">
            <div class="flex items-start justify-between gap-4 border-b border-neutral-100 p-6">
                <div>
                    <h2 id="delete-setoran-title" class="text-lg font-semibold text-neutral-900">Hapus Setoran</h2>
                    <p class="mt-1 text-sm text-neutral-500">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <button type="button" class="rounded-lg p-1.5 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-600 focus:outline-none focus:ring-2 focus:ring-primary-500" @click="showDeleteModal = false" aria-label="Tutup modal">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <p class="text-neutral-700">Apakah Anda yakin ingin menghapus setoran <span x-text="deleteItem?.name" class="font-medium"></span>?</p>
                <form method="POST" :action="'{{ route('ustadz.setorans.destroy', ':id') }}'.replace(':id', deleteItem?.id)">
                    @csrf
                    @method('DELETE')
                    <div class="mt-6 flex justify-end gap-2">
                        <x-button variant="ghost" @click="showDeleteModal = false">Batal</x-button>
                        <x-button type="submit" variant="destructive">Hapus</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
