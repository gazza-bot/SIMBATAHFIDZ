@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-neutral-900">{{ $pageTitle }}</h1>
        <p class="mt-1 text-sm text-neutral-500">Manajemen permintaan reset kata sandi oleh admin.</p>
    </div>

    @if (session('success'))
        <x-alert variant="success" class="rounded-xl">
            {{ session('success') }}
            @if (session('reset_link'))
                <p class="mt-2 break-all text-sm text-primary-700">
                    <code class="bg-primary-50 px-2 py-1 rounded">{{ session('reset_link') }}</code>
                </p>
            @endif
        </x-alert>
    @endif

    @if (session('error'))
        <x-alert variant="error" class="rounded-xl">
            {{ session('error') }}
        </x-alert>
    @endif

    <x-card padding="default">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-neutral-900">Daftar Permintaan</h3>
            <span class="text-sm text-neutral-500">{{ $requests->total() }} total</span>
        </div>

        @if ($requests->isEmpty())
            <div class="flex flex-col items-center gap-3 py-8 text-center">
                <div class="w-12 h-12 rounded-full bg-neutral-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2m-16 0H4m16 0v1a2 2 0 01-2 2H8a2 2 0 01-2-2v-1"/></svg>
                </div>
                <p class="text-sm text-neutral-500">Belum ada permintaan reset.</p>
                <p class="text-xs text-neutral-400">Permintaan baru dari halaman lupa kata sandi akan muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-neutral-200">
                <table class="w-full">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Peran</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Waktu Permintaan</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach ($requests as $item)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-4 py-3 text-sm font-mono text-neutral-900">{{ $item->username }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-900">{{ $item->user->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <x-badge variant="{{ $item->user->isAdmin() ? 'primary' : ($item->user->isKepalaTahfidz() ? 'info' : 'secondary') }}" size="xs">
                                        {{ str_replace('_', ' ', $item->user->role) }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-3 text-sm text-neutral-500">{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.password-requests.issue', $item->username) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 min-h-[44px] text-sm font-medium text-white bg-primary-500 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-lg px-3 py-1.5 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                Buat Link
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.password-requests.destroy', $item->username) }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 min-h-[44px] text-sm font-medium text-neutral-600 hover:text-error-700 hover:bg-error-50 focus:outline-none focus:ring-2 focus:ring-error-500 rounded-lg px-3 py-1.5 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <x-pagination :data="$requests" />
            </div>
        @endif
    </x-card>
</div>
@endsection