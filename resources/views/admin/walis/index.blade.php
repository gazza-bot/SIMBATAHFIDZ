@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')

    <div class="relative max-w-6xl mx-auto space-y-6" data-table-container x-data="adminFilter({ endpoint: '{{ route('admin.walis.index') }}' })">
        @include('admin.partials.table-loading')
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-neutral-900">{{ $pageTitle }}</h2>
                <p class="mt-1 text-sm text-neutral-500">Kelola data wali pesantren.</p>
            </div>
            <div class="flex gap-2">
                <x-button variant="secondary" onclick="toggleSidebar()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Sidebar
                </x-button>
                <x-button href="{{ route('admin.walis.create') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Wali
                </x-button>
            </div>
        </div>

        <form data-admin-filter action="{{ route('admin.walis.index') }}" method="GET">
            <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
                <div class="mb-4">
                    <x-input type="text" name="search" placeholder="Cari nama, username, atau nomor telepon..." value="{{ $filters['search'] ?? '' }}" />
                </div>
                <div class="flex justify-end gap-2">
                    <x-button type="submit" variant="primary">Terapkan</x-button>
                    <a href="{{ route('admin.walis.index') }}" class="text-sm text-primary-600 hover:underline">Reset</a>
                </div>
            </div>
        </form>

        <div x-html="html" class="px-1"></div>

        <div
            x-show="showDeleteModal" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-95"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-wali-title"
            @keydown.escape.window="showDeleteModal = false"
            x-ref="deleteWaliModal"
        >
            <div class="fixed inset-0 z-0 bg-neutral-950/70 backdrop-blur-sm" @click="showDeleteModal = false" aria-hidden="true"></div>
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-level-4">
                <div class="flex items-start justify-between gap-4 p-5 border-b border-neutral-100">
                    <div>
                        <h2 id="delete-wali-title" class="text-lg font-semibold text-neutral-900">Hapus Wali</h2>
                        <p class="mt-1 text-sm text-neutral-500">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <button
                        type="button"
                        class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                        @click="showDeleteModal = false"
                        aria-label="Tutup modal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5">
                    <p class="text-neutral-700">Apakah Anda yakin ingin menghapus wali <span x-text="deleteItem?.name" class="font-medium"></span>?</p>
                    <form method="POST" :action="'{{ route('admin.walis.destroy', ':id') }}'.replace(':id', deleteItem?.id)">
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