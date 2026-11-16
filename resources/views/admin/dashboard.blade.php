@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-6 sm:p-8 text-white">
            <div class="max-w-2xl">
                <p class="text-sm font-medium text-primary-100">Ringkasan pengelolaan pesantren</p>
                <h2 class="mt-2 text-xl sm:text-2xl font-bold">Assalamu'alaikum, {{ auth()->user()->name }}</h2>
                <p class="mt-1 text-sm sm:text-base text-primary-100">Pantau data master dan lanjutkan pekerjaan administrasi dari satu tempat.</p>
            </div>
        </div>

        <section aria-labelledby="data-summary-heading">
            <div class="flex items-center justify-between gap-4 mb-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-primary-600">Data master</p>
                    <h2 id="data-summary-heading" class="mt-1 text-lg font-bold text-neutral-900">Ringkasan saat ini</h2>
                </div>
                <p class="hidden sm:block text-sm text-neutral-500">Angka diperbarui dari data tersimpan.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                <div class="bg-white rounded-xl border border-neutral-200 p-5">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri</p>
                    <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalSantri }}</p>
                </div>
                <div class="bg-white rounded-xl border border-neutral-200 p-5">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Ustadz</p>
                    <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalUstadz }}</p>
                </div>
                <div class="bg-white rounded-xl border border-neutral-200 p-5">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Wali</p>
                    <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalWali }}</p>
                </div>
                <div class="bg-white rounded-xl border border-neutral-200 p-5">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Kelompok</p>
                    <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalKelompok }}</p>
                </div>
                <div class="col-span-2 bg-white rounded-xl border border-neutral-200 p-5 sm:col-span-1">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Angkatan</p>
                    <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalAngkatan }}</p>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-5">
            <section class="lg:col-span-3" aria-labelledby="quick-actions-heading">
                <div class="mb-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-secondary-600">Aksi cepat</p>
                    <h2 id="quick-actions-heading" class="mt-1 text-lg font-bold text-neutral-900">Tambah data master</h2>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('admin.santris.create') }}" class="flex min-h-16 items-center justify-between rounded-xl border border-neutral-200 bg-white px-4 py-3 transition-colors duration-fast hover:border-primary-300 hover:bg-primary-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <span>
                            <span class="block text-sm font-semibold text-neutral-900">Tambah Santri</span>
                            <span class="mt-0.5 block text-xs text-neutral-500">Daftarkan santri baru</span>
                        </span>
                        <span class="text-lg text-primary-500" aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.walis.create') }}" class="flex min-h-16 items-center justify-between rounded-xl border border-neutral-200 bg-white px-4 py-3 transition-colors duration-fast hover:border-primary-300 hover:bg-primary-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <span>
                            <span class="block text-sm font-semibold text-neutral-900">Tambah Wali</span>
                            <span class="mt-0.5 block text-xs text-neutral-500">Lengkapi data keluarga</span>
                        </span>
                        <span class="text-lg text-primary-500" aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.ustadzs.create') }}" class="flex min-h-16 items-center justify-between rounded-xl border border-neutral-200 bg-white px-4 py-3 transition-colors duration-fast hover:border-primary-300 hover:bg-primary-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <span>
                            <span class="block text-sm font-semibold text-neutral-900">Tambah Ustadz</span>
                            <span class="mt-0.5 block text-xs text-neutral-500">Atur data pengajar</span>
                        </span>
                        <span class="text-lg text-primary-500" aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.kelompoks.create') }}" class="flex min-h-16 items-center justify-between rounded-xl border border-neutral-200 bg-white px-4 py-3 transition-colors duration-fast hover:border-primary-300 hover:bg-primary-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <span>
                            <span class="block text-sm font-semibold text-neutral-900">Tambah Kelompok</span>
                            <span class="mt-0.5 block text-xs text-neutral-500">Siapkan halaqah baru</span>
                        </span>
                        <span class="text-lg text-primary-500" aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.angkatans.create') }}" class="flex min-h-16 items-center justify-between rounded-xl border border-neutral-200 bg-white px-4 py-3 transition-colors duration-fast hover:border-primary-300 hover:bg-primary-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 sm:col-span-2">
                        <span>
                            <span class="block text-sm font-semibold text-neutral-900">Tambah Angkatan</span>
                            <span class="mt-0.5 block text-xs text-neutral-500">Buat periode pembinaan baru</span>
                        </span>
                        <span class="text-lg text-primary-500" aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </section>

            <section class="lg:col-span-2" aria-labelledby="attention-heading">
                <div class="mb-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-accent-600">Pemeriksaan data</p>
                    <h2 id="attention-heading" class="mt-1 text-lg font-bold text-neutral-900">Perlu perhatian</h2>
                </div>
                <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-4 border-b border-neutral-100 pb-4">
                        <div>
                            <p class="text-sm font-semibold text-neutral-900">Santri belum lengkap</p>
                            <p class="mt-1 text-xs leading-5 text-neutral-500">Belum memiliki wali atau kelompok.</p>
                        </div>
                        <span class="text-xl font-bold text-secondary-600">{{ $unassignedSantri }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4 border-b border-neutral-100 py-4">
                        <div>
                            <p class="text-sm font-semibold text-neutral-900">Kelompok tanpa ustadz</p>
                            <p class="mt-1 text-xs leading-5 text-neutral-500">Perlu ditetapkan musyrif.</p>
                        </div>
                        <span class="text-xl font-bold text-secondary-600">{{ $unassignedKelompok }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4 pt-4">
                        <div>
                            <p class="text-sm font-semibold text-neutral-900">Angkatan aktif</p>
                            <p class="mt-1 text-xs leading-5 text-neutral-500">Periode pembinaan yang sedang berjalan.</p>
                        </div>
                        <span class="text-xl font-bold text-primary-600">{{ $activeAngkatan }}</span>
                    </div>
                </div>
            </section>
        </div>

        @if ($totalSantri === 0 && $totalUstadz === 0 && $totalWali === 0 && $totalKelompok === 0 && $totalAngkatan === 0)
            <div class="rounded-xl border border-dashed border-neutral-300 bg-neutral-50 p-5 text-center">
                <p class="text-sm font-semibold text-neutral-800">Belum ada data master</p>
                <p class="mt-1 text-sm text-neutral-500">Gunakan aksi cepat di atas untuk mulai mengisi data.</p>
            </div>
        @endif
    </div>
@endsection