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

    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-neutral-900">Daftar Permintaan</h3>
            <span class="text-sm text-neutral-500">{{ $requests->total() }} total</span>
        </div>

        @if ($requests->isEmpty())
            <div class="card-body">
                <p class="text-neutral-500 text-center py-8">Belum ada permintaan reset.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Username</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Peran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Ticket</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Dibuat</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-neutral-900">{{ $item->username }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-900">{{ $item->user->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <x-badge variant="{{ $item->user->is_admin ? 'primary' : ($item->user->is_kepala_tahfidz ? 'info' : 'secondary') }}" size="xs">
                                        {{ $item->user->role }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-3 text-sm font-mono text-neutral-600">{{ Str::limit($item->token ?? '', 10, '...') }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-500">{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <x-button variant="primary" size="sm" href="#" @click="alert('Link akan ditampilkan saat issue')">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                        Buat Link
                                    </x-button>
                                    <x-button variant="ghost" size="sm" @click="confirmDelete('{{ $item->username }}')">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Hapus
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-pagination :data="$requests" />
        @endif
    </div>
</div>

<script>
    function confirmDelete(username) {
        if (confirm('Hapus permintaan reset untuk ' + username + '?')) {
            fetch(document.querySelector('form[method="DELETE"]').action.replace(':id', username), {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            }).then(() => location.reload());
        }
    }
</script>
@endsection