<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Angkatan;
use App\Models\Kelompok;
use App\Models\KepalaTahfidz;
use App\Models\Santri;
use App\Models\Setoran;
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
        // Admin
        $adminUser = User::create([
            'name' => 'Admin Pesantren',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Kepala Tahfidz
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

        // Ustadz
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

        // Wali
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
                'wali_id' => $index === 0 ? $wali->id : null, // Santri pertama punya wali
                'status_aktif' => 'aktif',
                'tanggal_masuk' => $data['tanggal_masuk'],
            ]);
            $santris[] = $santri;
        }

        // 6. Setoran sample (ziyadah & murajaah)
        $surahs = Surah::where('nomor', '<=', 10)->get(); // Surah 1-10
        $now = Carbon::now();

        foreach ($santris as $santri) {
            // Ziyadah: Juz 1-3 (Surah 1-10)
            foreach (range(1, 3) as $juz) {
                $surah = $surahs->where('juz_mulai', '<=', $juz)->where('juz_selesai', '>=', $juz)->first();
                if ($surah) {
                    Setoran::create([
                        'santri_id' => $santri->id,
                        'ustadz_id' => $ustadz->id,
                        'tanggal' => $now->copy()->subDays(rand(1, 30)),
                        'jenis' => 'ziyadah',
                        'juz' => $juz,
                        'surah_id' => $surah->id,
                        'ayat_awal' => 1,
                        'ayat_akhir' => $surah->jumlah_ayat,
                        'nilai' => ['maqbul', 'jayyid', 'jayyid_jiddan', 'mumtaz'][array_rand(['maqbul', 'jayyid', 'jayyid_jiddan', 'mumtaz'])],
                        'catatan' => 'Setoran ziyadah juz ' . $juz,
                        'status' => 'approved',
                        'validated_by' => $ustadzUser->id,
                        'validated_at' => $now->copy()->subDays(rand(0, 5)),
                    ]);
                }
            }

            // Murajaah: Juz 1-2 (minggu ini)
            foreach (range(1, 2) as $juz) {
                $surah = $surahs->where('juz_mulai', '<=', $juz)->where('juz_selesai', '>=', $juz)->first();
                if ($surah) {
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
            }
        }

        // 7. Absensi sample (minggu ini)
        $days = ['2024-11-11', '2024-11-12', '2024-11-13', '2024-11-14', '2024-11-15'];
        foreach ($days as $day) {
            foreach ($santris as $santri) {
                $status = collect(['hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'])->random();
                Absensi::create([
                    'santri_id' => $santri->id,
                    'ustadz_id' => $ustadz->id,
                    'tanggal' => $day,
                    'status' => $status,
                    'catatan' => $status !== 'hadir' ? 'Izin orang tua' : null,
                ]);
            }
        }

        $this->command->info('Development data seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin / password');
        $this->command->info('Ustadz: ustadz / password');
        $this->command->info('Kepala Tahfidz: kepala / password');
        $this->command->info('Wali: wali / password');
    }
}