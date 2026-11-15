@props([
    'columns' => [],
    'data' => [],
    'key' => 'id',
    'striped' => true,
    'hoverable' => true,
    'selectable' => false,
    'emptyMessage' => 'Tidak ada data',
    'loading' => false,
    'pagination' => null,
    'onRowClick' => null,
    'attributes' => [],
])

<?php
$key = $key ?? 'id';
?>

<div class="overflow-x-auto rounded-2xl border border-neutral-200 bg-white">
    @if ($loading)
        <div class="p-8 text-center">
            <div class="inline-flex items-center gap-3 text-neutral-500">
                <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memuat data...</span>
            </div>
        </div>
    @elseif (count($data) === 0)
        <div class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="mt-3 text-neutral-500">{{ $emptyMessage }}</p>
        </div>
    @else
        <table class="w-full" role="grid">
            <thead class="bg-neutral-50 border-b border-neutral-200">
                <tr>
                    @if ($selectable)
                        <th scope="col" class="px-4 py-3 w-12">
                            <input type="checkbox" class="w-4 h-4 rounded border-neutral-300 text-primary-500 focus:ring-primary-500" aria-label="Pilih semua">
                        </th>
                    @endif
                    @foreach ($columns as $column)
                        <th
                            scope="col"
                            class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider {{ $column['class'] ?? '' }}"
                            {{ isset($column['width']) ? 'style="width: ' . $column['width'] . '"' : '' }}
                        >
                            {{ $column['label'] ?? ucfirst($column['key'] ?? '') }}
                        </th>
                    @endforeach
                    @if (isset($columns['actions']) || $onRowClick)
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach ($data as $row)
                    <tr
                        class="{{ $striped ? 'even:bg-neutral-50' : '' }} {{ $hoverable ? 'hover:bg-neutral-50' : '' }} {{ $onRowClick ? 'cursor-pointer' : '' }} transition-colors"
                        @if ($onRowClick) onclick="{{ $onRowClick }}('{{ $row->$key }}')" @endif
                        @if ($selectable) data-row-key="{{ $row->$key }}" @endif
                    >
                        @if ($selectable)
                            <td class="px-4 py-3">
                                <input type="checkbox" class="w-4 h-4 rounded border-neutral-300 text-primary-500 focus:ring-primary-500 row-checkbox" value="{{ $row->$key }}" aria-label="Pilih baris">
                            </td>
                        @endif
                        @foreach ($columns as $colKey => $column)
                            @if ($colKey !== 'actions')
                                <td class="px-4 py-3 text-sm text-neutral-700 {{ $column['class'] ?? '' }}">
                                    @if (isset($column['render']))
                                        {{ $column['render']($row) }}
                                    @else
                                        {{ $row->{$column['key'] ?? $colKey} ?? '' }}
                                    @endif
                                </td>
                            @endif
                        @endforeach
                        @if (isset($columns['actions']) || $onRowClick)
                            <td class="px-4 py-3 text-right">
                                @if (isset($columns['actions']))
                                    @foreach ($columns['actions'] as $action)
                                        <x-button
                                            variant="{{ $action['variant'] ?? 'ghost' }}"
                                            size="sm"
                                            class="gap-1.5"
                                            @if (isset($action['wire:click'])) wire:click="{{ $action['wire:click'] }}('{{ $row->$key }}')" @endif
                                            @if (isset($action['onclick'])) onclick="{{ $action['onclick'] }}('{{ $row->$key }}')" @endif
                                        >
                                            @if (isset($action['icon']))
                                                {{ $action['icon'] }}
                                            @endif
                                            {{ $action['label'] ?? '' }}
                                        </x-button>
                                    @endforeach
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@if ($pagination)
    <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-neutral-500">
            Menampilkan {{ $pagination->firstItem() }} sampai {{ $pagination->lastItem() }} dari {{ $pagination->total() }} data
        </div>
        <div class="flex gap-1">
            {{ $pagination->links() }}
        </div>
    </div>
@endif