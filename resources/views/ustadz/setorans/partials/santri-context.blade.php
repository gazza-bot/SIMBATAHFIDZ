@php
    $nilaiVariant = match ($santriContext['last_setoran']?->nilai) {
        'mumtaz' => 'mumtaz',
        'jayyid_jiddan' => 'jayyid_jiddan',
        'jayyid' => 'jayyid',
        'maqbul' => 'error',
        default => 'default',
    };
@endphp

<div class="rounded-2xl border border-neutral-200 bg-white shadow-level-1 overflow-hidden">
    <div class="p-6 sm:p-8">
        <div class="flex items-start justify-between gap-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Santri terpilih</p>
            <x-badge variant="success" size="xs" dot>Aktif</x-badge>
        </div>

        <div class="mt-4 flex items-start gap-3">
            <x-avatar :name="$selectedSantri->nama" size="lg" :photo="$selectedSantri->foto ?? null" />
            <div class="min-w-0 flex-1">
                <h3 class="text-lg font-bold text-neutral-900 leading-tight">{{ $selectedSantri->nama }}</h3>
                <p class="mt-1 text-sm text-neutral-500">NIS {{ $selectedSantri->nis }}</p>
                <p class="mt-0.5 text-sm text-neutral-600">
                    {{ $selectedSantri->kelompok->nama ?? 'Belum masuk kelompok' }}
                    @if ($selectedSantri->angkatan)
                        <span class="text-neutral-400">|</span> {{ $selectedSantri->angkatan->nama }}
                    @endif
                </p>
            </div>
        </div>

        <div class="mt-5 rounded-xl border border-neutral-100 bg-neutral-50 p-5">
            <div class="flex items-center justify-between gap-3 text-sm">
                <span class="font-medium text-neutral-700">Progres hafalan</span>
                <span class="text-neutral-600">Juz {{ $santriContext['progress_juz'] }} dari 30</span>
            </div>
            <div class="mt-2 h-2 overflow-hidden rounded-full bg-neutral-200">
                <div class="h-full rounded-full bg-primary-500" style="width: {{ $santriContext['progress_percent'] }}%"></div>
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-neutral-500">
                <span>{{ $santriContext['progress_percent'] }}% target 30 juz</span>
                <span>Target berikutnya Juz {{ $santriContext['next_juz'] }}</span>
            </div>
        </div>

        <div class="mt-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Capaian terakhir</p>
            @if ($santriContext['last_setoran'])
                <div class="mt-2 rounded-xl border border-neutral-100 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-neutral-900">
                                QS. {{ $santriContext['last_setoran']->surah->nama_latin ?? '-' }}
                            </p>
                            <p class="mt-0.5 text-xs text-neutral-500">
                                {{ $santriContext['last_setoran']->ayat_range }} | Juz {{ $santriContext['last_setoran']->juz }}
                            </p>
                            <p class="mt-1 text-xs text-neutral-500">
                                {{ $santriContext['last_setoran']->tanggal->format('d/m/Y') }} | {{ $santriContext['last_setoran']->jenis_label }}
                            </p>
                        </div>
                        <x-badge :variant="$nilaiVariant" size="sm">{{ $santriContext['last_setoran']->nilai_label }}</x-badge>
                    </div>
                    @if ($santriContext['last_setoran']->is_cover_penuh)
                        <p class="mt-2 text-xs font-medium text-success-700">Cover penuh surah</p>
                    @endif
                </div>
            @else
                <div class="mt-2 rounded-xl border border-dashed border-neutral-200 p-5 text-sm text-neutral-500">
                    Belum ada setoran yang tersimpan.
                </div>
            @endif
        </div>

        <div class="mt-5 grid grid-cols-3 gap-2">
            <div class="rounded-xl bg-neutral-50 p-3 text-center">
                <p class="text-[11px] font-medium uppercase tracking-wide text-neutral-500">Hafalan</p>
                <p class="mt-1 text-lg font-bold text-neutral-900">{{ $santriContext['progress_juz'] }}</p>
                <p class="text-[11px] text-neutral-500">Juz</p>
            </div>
            <div class="rounded-xl bg-neutral-50 p-3 text-center">
                <p class="text-[11px] font-medium uppercase tracking-wide text-neutral-500">Kehadiran</p>
                <p class="mt-1 text-lg font-bold text-neutral-900">
                    {{ $santriContext['attendance_rate'] !== null ? $santriContext['attendance_rate'] . '%' : '-' }}
                </p>
                <p class="text-[11px] text-neutral-500">
                    {{ $santriContext['attendance_total'] > 0 ? $santriContext['attendance_total'] . ' sesi' : 'Belum ada' }}
                </p>
            </div>
            <div class="rounded-xl bg-neutral-50 p-3 text-center">
                <p class="text-[11px] font-medium uppercase tracking-wide text-neutral-500">Rata nilai</p>
                <p class="mt-1 text-lg font-bold text-neutral-900">
                    {{ $santriContext['average_score'] !== null ? $santriContext['average_score'] : '-' }}
                </p>
                <p class="text-[11px] text-neutral-500">
                    {{ $santriContext['total_setoran'] }} setoran
                </p>
            </div>
        </div>
    </div>

    <div class="border-t border-neutral-100 bg-neutral-50 p-6 sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Panduan singkat</p>
        <ul class="mt-2 space-y-1.5 text-xs leading-relaxed text-neutral-600">
            <li>Pilih juz terlebih dahulu, lalu surah yang berada di juz tersebut.</li>
            <li>Rentang ayat mengikuti pembagian juz dan jumlah ayat surah.</li>
            <li>Setoran final langsung tercatat pada progres santri.</li>
        </ul>
    </div>
</div>
