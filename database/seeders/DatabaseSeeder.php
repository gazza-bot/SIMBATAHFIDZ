<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Angkatan;
use App\Models\HalaqahNote;
use App\Models\JuzStart;
use App\Models\Kelompok;
use App\Models\KepalaTahfidz;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\SimaanSchedule;
use App\Models\Surah;
use App\Models\Ustadz;
use App\Models\User;
use App\Models\Wali;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Surah (114 surah)
        $this->call(SurahSeeder::class);

        // 2. Angkatan
        $angkatans = collect([
            ['nama' => '2022/2023', 'tahun_mulai' => 2022, 'status_aktif' => true],
            ['nama' => '2023/2024', 'tahun_mulai' => 2023, 'status_aktif' => true],
            ['nama' => '2024/2025', 'tahun_mulai' => 2024, 'status_aktif' => true],
        ])->map(fn ($data) => Angkatan::create($data));

        // 3. Users & Roles
        User::create([
            'name' => 'Admin Pesantren',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $kepalaUser = User::create([
            'name' => 'Ust. Ahmad Dahlan, Lc.',
            'username' => 'kepala',
            'password' => bcrypt('password'),
            'role' => 'kepala_tahfidz',
        ]);
        KepalaTahfidz::create([
            'user_id' => $kepalaUser->id,
            'nama' => 'Ust. Ahmad Dahlan, Lc.',
        ]);

        $ustadzDefs = [
            ['name' => 'Ustadz Ahmad', 'username' => 'ustadz', 'nip' => '198001012005011001', 'telepon' => '081234567890'],
            ['name' => 'Ust. Muhammad Wildan, S.Pd.I', 'username' => 'ustadz2', 'nip' => '198502022010011002', 'telepon' => '081234567891'],
            ['name' => 'Ust. Salman Al-Farisi, Lc.', 'username' => 'ustadz3', 'nip' => '198703032012011003', 'telepon' => '081234567892'],
            ['name' => 'Ust. Rayhan Abdurrahman, M.Ag', 'username' => 'ustadz4', 'nip' => '198904042014011004', 'telepon' => '081234567893'],
            ['name' => 'Ust. Fathur Rabbani', 'username' => 'ustadz5', 'nip' => '199105052016011005', 'telepon' => '081234567894'],
        ];

        $ustadzs = collect($ustadzDefs)->map(function ($def) {
            $user = User::create([
                'name' => $def['name'],
                'username' => $def['username'],
                'password' => bcrypt('password'),
                'role' => 'ustadz',
            ]);

            return Ustadz::create([
                'user_id' => $user->id,
                'nip' => $def['nip'],
                'nama' => $def['name'],
                'telepon' => $def['telepon'],
            ]);
        });

        // 4. Wali
        $waliDefs = [
            ['name' => 'Bapak Santri', 'username' => 'wali', 'telepon' => '081234567811', 'alamat' => 'Ponorogo, Jawa Timur'],
            ['name' => 'Bapak Hasan', 'username' => 'wali2', 'telepon' => '081234567812', 'alamat' => 'Ponorogo, Jawa Timur'],
            ['name' => 'Bapak Ridwan', 'username' => 'wali3', 'telepon' => '081234567813', 'alamat' => 'Madiun, Jawa Timur'],
        ];

        $walis = collect($waliDefs)->map(function ($def) {
            $user = User::create([
                'name' => $def['name'],
                'username' => $def['username'],
                'password' => bcrypt('password'),
                'role' => 'wali',
            ]);

            return Wali::create([
                'user_id' => $user->id,
                'nama' => $def['name'],
                'telepon' => $def['telepon'],
                'alamat' => $def['alamat'],
            ]);
        });

        // 5. Kelompok Halaqah
        $kelompokDefs = [
            ['nama' => 'Halaqah Imami Nafi\'', 'deskripsi' => 'Halaqah reguler tingkat menengah'],
            ['nama' => 'Halaqah Imam \'Ashim', 'deskripsi' => 'Halaqah reguler tingkat menengah'],
            ['nama' => 'Halaqah Ibn Al-Jazari', 'deskripsi' => 'Halaqah takhassus tahfidz intensif'],
            ['nama' => 'Halaqah Imam Bukhari', 'deskripsi' => 'Halaqah reguler tingkat lanjut'],
            ['nama' => 'Halaqah Imam Ibnu Katsir', 'deskripsi' => 'Halaqah reguler tingkat lanjut'],
            ['nama' => 'Halaqah Imam Syatibi', 'deskripsi' => 'Halaqah persiapan tasmi akbar'],
        ];

        $kelompoks = collect($kelompokDefs)->map(function ($def, $index) use ($ustadzs, $angkatans) {
            return Kelompok::create([
                'nama' => $def['nama'],
                'ustadz_id' => $ustadzs[$index % $ustadzs->count()]->id,
                'angkatan_id' => $angkatans[$index % $angkatans->count()]->id,
                'deskripsi' => $def['deskripsi'],
            ]);
        });

        // 6. Santri (48 santri tersebar)
        $firstNames = ['Ahmad', 'Muhammad', 'Abdul', 'Fathur', 'Zainal', 'Iqbal', 'Syauqi', 'Malik', 'Fadhil', 'Zaki', 'Rizki', 'Rahman', 'Hafidz', 'Thariq', 'Ibrahim', 'Yusuf', 'Umar', 'Ali', 'Hasan', 'Husain', 'Salman', 'Bilal', 'Anas', 'Farhan'];
        $lastNames = ['Fauzi', 'Rizki', 'Rahman', 'Rohman', 'Abidin', 'Iqbal', 'Syauqi', 'Malik', 'Fadhil', 'Zaki', 'Hafidz', 'Naufal', 'Aziz', 'Karim', 'Hakim', 'Siddiq'];

        $santris = collect();
        for ($i = 0; $i < 48; $i++) {
            $nama = $firstNames[$i % count($firstNames)] . ' ' . $lastNames[intdiv($i, count($firstNames)) % count($lastNames)];
            $kelompok = $kelompoks[$i % $kelompoks->count()];

            $waliId = null;
            if ($i < 5) {
                $waliId = $walis[0]->id;
            } elseif ($i % 4 === 0) {
                $waliId = $walis[$i % $walis->count()]->id;
            }

            $santris->push(Santri::create([
                'nis' => '2024' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'nama' => $nama,
                'angkatan_id' => $kelompok->angkatan_id,
                'kelompok_id' => $kelompok->id,
                'wali_id' => $waliId,
                'status_aktif' => 'aktif',
                'tanggal_masuk' => Carbon::create(2024, 7 + ($i % 3), 1 + ($i % 20))->toDateString(),
            ]));
        }

        $santris = Santri::with('kelompok.ustadz')->get();

        // 7. Juz starts
        $this->seedJuzStarts();

        // 8. Setoran sample
        $this->seedSetorans($santris);

        // 9. Absensi sample (30 hari terakhir)
        $this->seedAbsensi($santris);

        // 10. Halaqah notes
        $this->seedHalaqahNotes($kelompoks, $ustadzs);

        // 11. Simaan schedules
        $this->seedSimaanSchedules($santris);

        $this->command->info('Development data seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin / password');
        $this->command->info('Ustadz: ustadz / password');
        $this->command->info('Kepala Tahfidz: kepala / password');
        $this->command->info('Wali: wali / password');
    }

    private function seedJuzStarts(): void
    {
        $juzStarts = [
            ['juz' => 1, 'surah_id' => 1, 'ayat' => 1],
            ['juz' => 2, 'surah_id' => 2, 'ayat' => 142],
            ['juz' => 3, 'surah_id' => 2, 'ayat' => 253],
            ['juz' => 4, 'surah_id' => 3, 'ayat' => 93],
            ['juz' => 5, 'surah_id' => 4, 'ayat' => 24],
            ['juz' => 6, 'surah_id' => 4, 'ayat' => 148],
            ['juz' => 7, 'surah_id' => 5, 'ayat' => 82],
            ['juz' => 8, 'surah_id' => 6, 'ayat' => 111],
            ['juz' => 9, 'surah_id' => 7, 'ayat' => 88],
            ['juz' => 10, 'surah_id' => 8, 'ayat' => 41],
            ['juz' => 11, 'surah_id' => 9, 'ayat' => 93],
            ['juz' => 12, 'surah_id' => 11, 'ayat' => 6],
            ['juz' => 13, 'surah_id' => 12, 'ayat' => 53],
            ['juz' => 14, 'surah_id' => 15, 'ayat' => 1],
            ['juz' => 15, 'surah_id' => 17, 'ayat' => 1],
            ['juz' => 16, 'surah_id' => 18, 'ayat' => 75],
            ['juz' => 17, 'surah_id' => 21, 'ayat' => 1],
            ['juz' => 18, 'surah_id' => 23, 'ayat' => 1],
            ['juz' => 19, 'surah_id' => 25, 'ayat' => 21],
            ['juz' => 20, 'surah_id' => 27, 'ayat' => 56],
            ['juz' => 21, 'surah_id' => 29, 'ayat' => 46],
            ['juz' => 22, 'surah_id' => 33, 'ayat' => 31],
            ['juz' => 23, 'surah_id' => 36, 'ayat' => 28],
            ['juz' => 24, 'surah_id' => 39, 'ayat' => 32],
            ['juz' => 25, 'surah_id' => 41, 'ayat' => 47],
            ['juz' => 26, 'surah_id' => 46, 'ayat' => 1],
            ['juz' => 27, 'surah_id' => 51, 'ayat' => 31],
            ['juz' => 28, 'surah_id' => 58, 'ayat' => 1],
            ['juz' => 29, 'surah_id' => 67, 'ayat' => 1],
            ['juz' => 30, 'surah_id' => 78, 'ayat' => 1],
        ];

        foreach ($juzStarts as $j) {
            JuzStart::create($j);
        }
    }

    private function seedSetorans($santris): void
    {
        $now = Carbon::now();
        $surahs = Surah::all();

        // Pola target juz: sebagian khatim, sebagian menengah, sebagian awal.
        $progressPattern = [30, 30, 30, 28, 26, 24, 22, 20, 18, 16, 14, 12, 10, 10, 9, 8, 8, 7, 7, 6, 6, 5, 5, 4, 4, 3, 3, 3, 2, 2, 1, 1];
        $nilaiPool = ['maqbul', 'jayyid', 'jayyid', 'jayyid_jiddan', 'jayyid_jiddan', 'mumtaz'];

        foreach ($santris as $index => $santri) {
            $ustadzId = $santri->kelompok?->ustadz_id;
            if (! $ustadzId) {
                continue;
            }

            $target = $progressPattern[$index % count($progressPattern)];

            for ($juz = 1; $juz <= $target; $juz++) {
                $surah = $surahs->first(fn ($s) => $s->juz_mulai <= $juz && $s->juz_selesai >= $juz);
                if (! $surah) {
                    continue;
                }

                Setoran::create([
                    'santri_id' => $santri->id,
                    'ustadz_id' => $ustadzId,
                    'tanggal' => $now->copy()->subDays(rand(1, 150)),
                    'jenis' => 'ziyadah',
                    'juz' => $juz,
                    'surah_id' => $surah->id,
                    'ayat_awal' => 1,
                    'ayat_akhir' => $surah->jumlah_ayat,
                    'nilai' => $nilaiPool[array_rand($nilaiPool)],
                    'catatan' => 'Setoran ziyadah juz ' . $juz,
                    'status' => 'approved',
                    'validated_by' => $santri->kelompok->ustadz->user_id ?? null,
                    'validated_at' => $now->copy()->subDays(rand(0, 30)),
                ]);
            }

            // Murajaah: 70% santri punya murajaah terbaru, sisanya tertunda.
            if ($index % 10 < 7) {
                $murajaahJuz = max(1, $target - 1);
                $surah = $surahs->first(fn ($s) => $s->juz_mulai <= $murajaahJuz && $s->juz_selesai >= $murajaahJuz);
                if ($surah) {
                    Setoran::create([
                        'santri_id' => $santri->id,
                        'ustadz_id' => $ustadzId,
                        'tanggal' => $now->copy()->subDays(rand(0, 10)),
                        'jenis' => 'murajaah',
                        'juz' => $murajaahJuz,
                        'surah_id' => $surah->id,
                        'ayat_awal' => 1,
                        'ayat_akhir' => $surah->jumlah_ayat,
                        'nilai' => $nilaiPool[array_rand($nilaiPool)],
                        'catatan' => 'Murajaah juz ' . $murajaahJuz,
                        'status' => 'approved',
                        'validated_by' => $santri->kelompok->ustadz->user_id ?? null,
                        'validated_at' => $now->copy()->subDays(rand(0, 5)),
                    ]);
                }
            } elseif ($index % 2 === 0) {
                $murajaahJuz = max(1, $target - 1);
                $surah = $surahs->first(fn ($s) => $s->juz_mulai <= $murajaahJuz && $s->juz_selesai >= $murajaahJuz);
                if ($surah) {
                    Setoran::create([
                        'santri_id' => $santri->id,
                        'ustadz_id' => $ustadzId,
                        'tanggal' => $now->copy()->subDays(rand(20, 45)),
                        'jenis' => 'murajaah',
                        'juz' => $murajaahJuz,
                        'surah_id' => $surah->id,
                        'ayat_awal' => 1,
                        'ayat_akhir' => $surah->jumlah_ayat,
                        'nilai' => $nilaiPool[array_rand($nilaiPool)],
                        'catatan' => 'Murajaah tertunda juz ' . $murajaahJuz,
                        'status' => 'approved',
                        'validated_by' => $santri->kelompok->ustadz->user_id ?? null,
                        'validated_at' => $now->copy()->subDays(rand(20, 45)),
                    ]);
                }
            }

            // Setoran hari ini agar indikator sesi dan pemantauan halaqah terisi.
            if ($index % 3 !== 0) {
                $juzToday = max(1, min(30, $target));
                $surahToday = $surahs->first(fn ($s) => $s->juz_mulai <= $juzToday && $s->juz_selesai >= $juzToday);
                if ($surahToday) {
                    Setoran::create([
                        'santri_id' => $santri->id,
                        'ustadz_id' => $ustadzId,
                        'tanggal' => $now->copy()->toDateString(),
                        'jenis' => 'murajaah',
                        'juz' => $juzToday,
                        'surah_id' => $surahToday->id,
                        'ayat_awal' => 1,
                        'ayat_akhir' => $surahToday->jumlah_ayat,
                        'nilai' => $nilaiPool[array_rand($nilaiPool)],
                        'catatan' => 'Murajaah sesi hari ini',
                        'status' => 'approved',
                        'validated_by' => $santri->kelompok->ustadz->user_id ?? null,
                        'validated_at' => $now,
                    ]);
                }
            }

            // Tasmi sesekali
            if ($index % 4 === 0) {
                $tasmiJuz = max(1, min(30, $target));
                $surah = $surahs->first(fn ($s) => $s->juz_mulai <= $tasmiJuz && $s->juz_selesai >= $tasmiJuz);
                if ($surah) {
                    Setoran::create([
                        'santri_id' => $santri->id,
                        'ustadz_id' => $ustadzId,
                        'tanggal' => $now->copy()->subDays(rand(1, 20)),
                        'jenis' => 'tasmi',
                        'juz' => $tasmiJuz,
                        'surah_id' => $surah->id,
                        'ayat_awal' => 1,
                        'ayat_akhir' => $surah->jumlah_ayat,
                        'nilai' => 'jayyid_jiddan',
                        'catatan' => 'Tasmi juz ' . $tasmiJuz,
                        'status' => 'approved',
                        'validated_by' => $santri->kelompok->ustadz->user_id ?? null,
                        'validated_at' => $now->copy()->subDay(),
                    ]);
                }
            }
        }
    }

    private function seedAbsensi($santris): void
    {
        $statusPool = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit'];
        $sesiPool = ['subuh', 'ashar', 'maghrib'];
        $waktuMap = ['subuh' => '05:00', 'ashar' => '14:30', 'maghrib' => '18:30'];

        for ($d = 0; $d < 30; $d++) {
            $date = Carbon::today()->subDays($d)->toDateString();

            foreach ($santris as $santri) {
                if (rand(1, 100) > 94) {
                    continue;
                }

                $status = $statusPool[array_rand($statusPool)];
                $sesi = $sesiPool[array_rand($sesiPool)];

                Absensi::create([
                    'santri_id' => $santri->id,
                    'ustadz_id' => $santri->kelompok?->ustadz_id,
                    'tanggal' => $date,
                    'sesi' => $sesi,
                    'waktu_tiba' => $waktuMap[$sesi],
                    'status' => $status,
                    'catatan' => $status !== 'hadir' ? 'Keterangan ' . $status : null,
                ]);
            }
        }
    }

    private function seedHalaqahNotes($kelompoks, $ustadzs): void
    {
        foreach ($kelompoks as $index => $kelompok) {
            HalaqahNote::create([
                'kelompok_id' => $kelompok->id,
                'ustadz_id' => $ustadzs[$index % $ustadzs->count()]->id,
                'tanggal' => now()->subDay()->toDateString(),
                'catatan' => 'Halaqah kondusif. Setoran berjalan sesuai target harian.',
            ]);
        }
    }

    private function seedSimaanSchedules($santris): void
    {
        foreach ($santris->take(12) as $santri) {
            SimaanSchedule::create([
                'santri_id' => $santri->id,
                'ustadz_id' => $santri->kelompok?->ustadz_id,
                'tanggal' => now()->addDay()->toDateString(),
                'keterangan' => 'Sima\'an ziyadah rutin',
                'status' => 'dijadwalkan',
            ]);
        }
    }
}
