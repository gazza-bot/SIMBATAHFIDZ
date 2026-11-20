@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-5 sm:p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold">Assalamu'alaikum, {{ auth()->user()->name }}</h2>
                <p class="mt-1 text-sm text-primary-100">Pantau capaian hafalan seluruh santri dari satu tempat.</p>
            </div>
            <div class="flex items-center gap-2 bg-white/10 rounded-lg px-3 py-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-xs text-primary-100">{{ now()->format('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <x-card padding="8" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri Aktif</p>
            <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalSantri }}</p>
        </x-card>
        <x-card padding="8" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Kelompok Halaqah</p>
            <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalKelompok }}</p>
        </x-card>
        <x-card padding="8" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Setoran Bulan Ini</p>
            <p class="mt-2 text-2xl font-bold text-success-600">{{ $totalSetoranThisMonth }}</p>
        </x-card>
        <x-card padding="8" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Rata-rata Kehadiran</p>
            <p class="mt-2 text-2xl font-bold text-warning-600">{{ $attendanceRate }}%</p>
        </x-card>
    </div>

    <!-- Chart Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Progress Hafalan per Juz -->
        <x-card padding="lg" class="bg-white border border-neutral-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-neutral-900">Progress Hafalan per Juz</h3>
                <span class="text-xs text-neutral-500">{{ $totalSantri }} santri</span>
            </div>
            <div class="bg-neutral-50 rounded-xl p-3 min-h-[280px]" id="chart-juz">
                <canvas id="juzChart"></canvas>
            </div>
        </x-card>

        <!-- Kehadiran 30 Hari -->
        <x-card padding="lg" class="bg-white border border-neutral-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-neutral-900">Kehadiran 30 Hari Terakhir</h3>
                <span class="text-xs text-neutral-500">Persentase harian</span>
            </div>
            <div class="bg-neutral-50 rounded-xl p-3 min-h-[280px]" id="chart-attendance">
                <canvas id="attendanceChart"></canvas>
            </div>
        </x-card>
    </div>

    <!-- Mutu Distribution + Attention List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Mutu Distribution -->
        <div class="lg:col-span-1 bg-white rounded-2xl border border-neutral-200 p-4 sm:p-5">
            <h3 class="text-base font-semibold text-neutral-900 mb-4">Distribusi Mutu</h3>
            <div class="bg-neutral-50 rounded-xl p-3 min-h-[200px]" id="chart-muti">
                <canvas id="mutuChart"></canvas>
            </div>
        </div>

        <!-- Attention List -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-neutral-200 p-4 sm:p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-neutral-900">Santri Perlu Perhatian</h3>
                <span class="text-xs text-neutral-500">Kriteria: kekeruhan < 75% / belum murajaah 14+ hari / progres < Juz 3</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" role="grid">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Kelompok</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Ustadz</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Juz Terkini</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($attentionSantris as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-neutral-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $item['santri']->nama }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $item['kelompok'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-600">{{ $item['ustadz'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $item['santri']->progress_juz >= 10 ? 'bg-success-50 text-success-700' : ($item['santri']->progress_juz >= 5 ? 'bg-warning-50 text-warning-700' : 'bg-error-50 text-error-700') }}">
                                        Juz {{ $item['santri']->progress_juz }}
                                        <x-badge variant="default" size="xs" dot class="hidden" />
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-error-600">
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach ($item['reasons'] as $reason)
                                            <li>{{ $reason }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-neutral-500">
                                    <svg class="w-10 h-10 mx-auto text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <p>Tidak ada santri yang membutuhkan perhatian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    // Juz progress chart
    const juzCtx = document.getElementById('juzChart')?.getContext('2d');
    if (juzCtx) {
        new Chart(juzCtx, {
            type: 'bar',
            data: {
                labels: @json($juzLabels),
                datasets: [{
                    label: 'Jumlah Santri',
                    data: @json($juzData),
                    backgroundColor: @json($juzBgColors),
                    borderColor: @json($juzBorderColors),
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.raw} santri`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { family: 'Nunito Sans' } },
                        grid: { color: 'rgba(71, 85, 78, 0.12)' }
                    },
                    x: {
                        ticks: { font: { family: 'Nunito Sans', size: 10 } },
                        grid: { display: false }
                    }
                },
                animation: { duration: 0 }
            }
        });
    }

    // Attendance 30 days chart
    const attendanceCtx = document.getElementById('attendanceChart')?.getContext('2d');
    if (attendanceCtx) {
        new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: @json($attendanceLabels),
                datasets: [{
                    label: 'Persentase Kehadiran (%)',
                    data: @json($attendanceRates),
                    backgroundColor: 'rgba(74, 124, 89, 0.1)',
                    borderColor: '#4A7C59',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4A7C59',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.raw}% kehadiran (${ctx.parsed.x})`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { stepSize: 20, font: { family: 'Nunito Sans' } },
                        grid: { color: 'rgba(71, 85, 78, 0.12)' }
                    },
                    x: {
                        ticks: { font: { family: 'Nunito Sans', size: 9 } },
                        grid: { display: false }
                    }
                },
                animation: { duration: 0 }
            }
        });
    }

    // Mutu distribution chart
    const mutuCtx = document.getElementById('mutuChart')?.getContext('2d');
    if (mutuCtx) {
        new Chart(mutuCtx, {
            type: 'doughnut',
            data: {
                labels: @json($mutuLabels),
                datasets: [{
                    data: @json($mutuData),
                    backgroundColor: [
                        '#7C9B7E',  // maqbul - muted green
                        '#C87D46',  // jayyid - terra
                        '#EAB308',  // jayyid_jiddan - gold
                        '#16A34A',  // mumtaz - success green
                    ],
                    borderColor: '#FFFFFF',
                    borderWidth: 2,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Nunito Sans', size: 11 },
                            padding: 12,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.label}: ${ctx.raw} santri`
                        }
                    }
                },
                animation: { duration: 0 }
            }
        });
    }
}
</script>
@endsection