@props([
    'title',
    'itemLabel',
    'action',
])

<div
    x-show="showDeleteModal"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="delete-modal-title"
    @keydown.escape.window="closeDeleteModal()"
>

    <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-level-4">
        <div class="flex items-start justify-between gap-4 p-5 border-b border-neutral-100">
            <div>
                <h2 id="delete-modal-title" class="text-lg font-semibold text-neutral-900">{{ $title }}</h2>
                <p class="mt-1 text-sm text-neutral-500">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <button
                type="button"
                class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                @click="closeDeleteModal()"
                aria-label="Tutup modal"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-5">
            <p class="text-neutral-700">
                Apakah Anda yakin ingin menghapus {{ $itemLabel }}
                <span x-text="deleteItem?.name" class="font-medium"></span>?
            </p>
            <form method="POST" :action="deleteItem ? '{{ $action }}'.replace(':id', deleteItem.id) : '#'">
                @csrf
                @method('DELETE')
                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" variant="ghost" @click="closeDeleteModal()">Batal</x-button>
                    <x-button type="submit" variant="destructive">Hapus</x-button>
                </div>
            </form>
        </div>
    </div>
    <div class="fixed inset-0 z-0 bg-neutral-950/70 backdrop-blur-sm" @click="closeDeleteModal()" aria-hidden="true"></div>
</div>
