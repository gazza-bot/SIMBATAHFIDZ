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
        $angkatan = Angkatan::create([
            'nama' => '2024/2025',
            'tahun_mulai' => 2024,
            'status_aktif' => true,
        ]);

        // 3. Users & Roles
        $adminUser = User::create([
            'name' => 'Admin Pesantren',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $kepalaUser = User::create([
            'name' => 'Kepala Tahfidz',
            'username' => 'kepala',
            'password' => bcrypt('password'),
            'role' => 'kepala_tahfidz',
        ]);
        KepalaTahfidz::create([
            'user_id' => $kepalaUser->id,
            'nama' => 'Kepala Tahfidz',
        ]);

        $ustadzUser = User::create([
            'name' => 'Ustadz Ahmad',
            'username' => 'ustadz',
            'password' => bcrypt('password'),
            'role' => 'ustadz',
        ]);
        $ustadz = Ustadz::create([
            'user_id' => $ustadzUser->id,
            'nip' => '198001012005011001',
            'nama' => 'Ustadz Ahmad',
            'telepon' => '081234567890',
        ]);

        $waliUser = User::create([
            'name' => 'Bapak Santri',
            'username' => 'wali',
            'password' => bcrypt('password'),
            'role' => 'wali',
        ]);
        $wali = Wali::create([
            'user_id' => $waliUser->id,
            'nama' => 'Bapak Santri',
            'telepon' => '081234567891',
            'alamat' => 'Ponorogo, Jawa Timur',
        ]);

        // 4. Kelompok
        $kelompok = Kelompok::create([
            'nama' => 'Halaqah A',
            'ustadz_id' => $ustadz->id,
            'angkatan_id' => $angkatan->id,
            'deskripsi' => 'Halaqah pemula untuk santri baru',
        ]);

        // 5. Santri (10 santri)
        $santriData = [
            ['nis' => '2024001', 'nama' => 'Ahmad Fauzi', 'tanggal_masuk' => '2024-07-01'],
            ['nis' => '2024002', 'nama' => 'Muhammad Rizki', 'tanggal_masuk' => '2024-07-01'],
            ['nis' => '2024003', 'nama' => 'Abdul Rahman', 'tanggal_masuk' => '2024-07-01'],
            ['nis' => '2024004', 'nama' => 'Fathur Rohman', 'tanggal_masuk' => '2024-07-01'],
            ['nis' => '2024005', 'nama' => 'Zainal Abidin', 'tanggal_masuk' => '2024-07-01'],
            ['nis' => '2024006', 'nama' => 'Muhammad Iqbal', 'tanggal_masuk' => '2024-07-15'],
            ['nis' => '2024007', 'nama' => 'Ahmad Syauqi', 'tanggal_masuk' => '2024-07-15'],
            ['nis' => '2024008', 'nama' => 'Abdul Malik', 'tanggal_masuk' => '2024-08-01'],
            ['nis' => '2024009', 'nama' => 'Muhammad Fadhil', 'tanggal_masuk' => '2024-08-01'],
            ['nis' => '2024010', 'nama' => 'Ahmad Zaki', 'tanggal_masuk' => '2024-08-01'],
        ];

        $santris = [];
        foreach ($santriData as $index => $data) {
            $santri = Santri::create([
                'nis' => $data['nis'],
                'nama' => $data['nama'],
                'angkatan_id' => $angkatan->id,
                'kelompok_id' => $kelompok->id,
                'wali_id' => $index === 0 ? $wali->id : null,
                'status_aktif' => 'aktif',
                'tanggal_masuk' => $data['tanggal_masuk'],
            ]);
            $santris[] = $santri;
        }

        // 6. Juz starts
        $this->seedJuzStarts();

        // 7. Setoran sample
        $this->seedSetorans($santris, $ustadz, $ustadzUser);

        // 8. Absensi sample (multi sesi)
        $this->seedAbsensi($santris, $ustadz);

        // 9. Halaqah notes
        $this->seedHalaqahNotes($kelompok, $ustadz);

        // 10. Simaan schedules
        $this->seedSimaanSchedules($santris, $ustadz);

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

    private function seedSetorans(array $santris, Ustadz $ustadz, User $ustadzUser): void
    {
        $now = Carbon::now();
        $surahs = Surah::where('nomor', '<=', 10)->get();

        foreach ($santris as $santri) {
            // Ziyadah approved
            foreach (range(1, 3) as $juz) {
                $surah = $surahs->where('juz_mulai', '<=', $juz)->where('juz_selesai', '>=', $juz)->first();
                if (!$surah) {
                    continue;
                }
                Setoran::create([
                    'santri_id' => $santri->id,
                    'ustadz_id' => $ustadz->id,
                    'tanggal' => $now->copy()->subDays(rand(1, 30)),
                    'jenis' => 'ziyadah',
                    'juz' => $juz,
                    'surah_id' => $surah->id,
                    'ayat_awal' => 1,
                    'ayat_akhir' => $surah->jumlah_ayat,
                    'nilai' => $this->randomNilai(),
                    'catatan' => 'Setoran ziyadah juz ' . $juz,
                    'status' => 'approved',
                    'validated_by' => $ustadzUser->id,
                    'validated_at' => $now->copy()->subDays(rand(0, 5)),
                ]);
            }

            // Murajaah approved
            foreach (range(1, 2) as $juz) {
                $surah = $surahs->where('juz_mulai', '<=', $juz)->where('juz_selesai', '>=', $juz)->first();
                if (!$surah) {
                    continue;
                }
                Setoran::create([
                    'santri_id' => $santri->id,
                    'ustadz_id' => $ustadz->id,
                    'tanggal' => $now->copy()->subDays(rand(1, 7)),
                    'jenis' => 'murajaah',
                    'juz' => $juz,
                    'surah_id' => $surah->id,
                    'ayat_awal' => 1,
                    'ayat_akhir' => $surah->jumlah_ayat,
                    'nilai' => 'jayyid',
                    'catatan' => 'Murajaah juz ' . $juz,
                    'status' => 'approved',
                    'validated_by' => $ustadzUser->id,
                    'validated_at' => $now->copy()->subDays(rand(0, 2)),
                ]);
            }

            Setoran::create([
                'santri_id' => $santri->id,
                'ustadz_id' => $ustadz->id,
                'tanggal' => $now->copy()->subDays(rand(2, 5)),
                'jenis' => 'ziyadah',
                'juz' => 4,
                'surah_id' => $surahs->where('juz_mulai', '<=', 4)->where('juz_selesai', '>=', 4)->first()->id,
                'ayat_awal' => 1,
                'ayat_akhir' => 15,
                'nilai' => 'maqbul',
                'catatan' => 'Setoran ziyadah juz 4',
                'is_draft' => false,
                'status' => 'approved',
                'validated_by' => $ustadzUser->id,
                'validated_at' => $now->copy()->subDays(2),
            ]);

            Setoran::create([
                'santri_id' => $santri->id,
                'ustadz_id' => $ustadz->id,
                'tanggal' => $now->copy()->subDays(rand(1, 3)),
                'jenis' => 'tasmi',
                'juz' => 30,
                'surah_id' => Surah::where('nomor', '<=', 30)->first()->id,
                'ayat_awal' => 1,
                'ayat_akhir' => 23,
                'nilai' => 'jayyid_jiddan',
                'catatan' => 'Tasmi pekan ini',
                'is_draft' => false,
                'status' => 'approved',
                'validated_by' => $ustadzUser->id,
                'validated_at' => $now->copy()->subDay(),
            ]);
        }
    }

    private function seedAbsensi(array $santris, Ustadz $ustadz): void
    {
        $days = ['2024-11-11', '2024-11-12', '2024-11-13', '2024-11-14', '2024-11-15'];
        foreach ($days as $day) {
            foreach ($santris as $santri) {
                $status = collect(['hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'])->random();
                $sesi = collect(['subuh', 'ashar', 'maghrib'])->random();
                $waktu = ['subuh' => '05:00', 'ashar' => '14:30', 'maghrib' => '18:30'][$sesi];
                Absensi::create([
                    'santri_id' => $santri->id,
                    'ustadz_id' => $ustadz->id,
                    'tanggal' => $day,
                    'sesi' => $sesi,
                    'waktu_tiba' => $waktu,
                    'status' => $status,
                    'catatan' => $status !== 'hadir' ? 'Izin orang tua' : null,
                ]);
            }
        }
    }

    private function seedHalaqahNotes(Kelompok $kelompok, Ustadz $ustadz): void
    {
        HalaqahNote::create([
            'kelompok_id' => $kelompok->id,
            'ustadz_id' => $ustadz->id,
            'tanggal' => now()->subDay()->toDateString(),
            'catatan' => 'Halaqah kondusif. Bacaan juz 1-3 tuntas.',
        ]);
    }

    private function seedSimaanSchedules(array $santris, Ustadz $ustadz): void
    {
        foreach ($santris as $santri) {
            SimaanSchedule::create([
                'santri_id' => $santri->id,
                'ustadz_id' => $ustadz->id,
                'tanggal' => now()->addDay()->toDateString(),
                'keterangan' => 'Sima\'an ziyadah juz 4-5',
                'status' => 'dijadwalkan',
            ]);
        }
    }

    private function randomNilai(): string
    {
        return ['maqbul', 'jayyid', 'jayyid_jiddan', 'mumtaz'][array_rand(['maqbul', 'jayyid', 'jayyid_jiddan', 'mumtaz'])];
    }
}