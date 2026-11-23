# SIMBA TAHFIDZ - Sprint Roadmap

Sistem Informasi Mutaba'ah & Belajar Al-Qur'an — Pondok Pesantren Darut Taqwa Ponorogo.

## Sprint 0 — Fondasi
- [x] Laravel 12 + Tailwind CSS v4 + design tokens (`resources/css/app.css`)
- [x] Custom auth username/password + role middleware (admin, ustadz, kepala_tahfidz, wali)
- [x] Migrasi 11 tabel + seeder (114 surah, data dev, 4 akun login)
- [x] Blade components (button, card, badge, avatar, table, dst.) + 3 layouts
- [x] Halaman login tampil + redirect per peran
- [x] Dashboard placeholder 4 peran (data nyata dari seed)

## Sprint 1 — Kelola Data Master (Admin)
- [x] Master Switcher Tabs UI dihapus agar halaman master data tetap ringkas
- [x] CRUD Santri: index/create/store/edit/update/destroy (+ cari, filter kelompok/angkatan, relasi wali)
- [x] CRUD Wali: index/create/store/edit/update/destroy (+ relasi santri, no HP, alamat)
- [x] CRUD Ustadz: index/create/store/edit/update/destroy (+ NIP, no HP, angkatan dibimbing)
- [x] CRUD Kelompok Halaqah: index/create/store/edit/update/destroy (+ relasi ustadz musyrif, angkatan, kapasitas)
- [x] CRUD Angkatan: index/create/store/edit/update/destroy (+ nama, tahun, status aktif)
- [x] Validasi relasi: santri↔wali↔kelompok↔ustadz↔angkatan (FK integrity)
- [x] Daftar & edit profil diri (Admin)
- [x] Empty/loading/error states pada semua tabel (R-27)

## Sprint 2 — Input Setoran (Ustadz)
- [x] Form input setoran hafalan (santri, surah, ayat awal-akhir, nilai)
- [x] UI dua kolom: kartu konteks santri, progres, kehadiran, rata nilai, dan setoran hari ini
- [x] Indikator cover penuh surah (Mutqin) dihitung otomatis dari rentang ayat
- [x] Setoran final langsung disetujui (auto-approve) dan masuk progres santri
- [x] Halaman sukses dan daftar setoran responsif tanpa fitur draft

## Sprint 3 — Absensi Kelompok (Ustadz)
- [x] Presensi harian (Hadir/Izin/Sakit/Alpa)
- [x] Rekap absensi kelompok
- [x] Empty/loading/error states (R-27)
- [x] Mobile 44px tap targets (R-03)
- [x] Keyboard accessible modal (R-32, R-39)

## Sprint 4 — Dasbor Kepala Tahfidz
- [x] Indikator utama harian + capaian keseluruhan (data nyata)
- [x] Pemantauan halaqah: progress setoran, rata mutu, kehadiran
- [x] Peta Hafalan 30 Juz (grid persentase + filter angkatan)
- [x] Daftar santri perlu perhatian (kehadiran < 75%, belum murajaah > 14 hari, progres < Juz 3)
- [x] Aktivitas setoran & tasmi terkini + santri menuju khatam
- [x] Layout disesuaikan dengan UI_SIMBATAHFIDZ (tanpa Chart.js)

## Sprint 5 — Dasbor Wali
- [x] Mutaba'ah anak (tab multi-anak)
- [x] Riwayat setoran & absensi
- [x] Peta Hafalan 30 Juz & amalan yaumiyah santri
- [x] Pesan kasih orang tua interaktif (wali_messages)
- [x] Layout desktop & mobile disesuaikan dengan UI_SIMBATAHFIDZ

## Sprint 6 — Peta Hafalan 30 Juz
- [x] Grid 30 juz (Mutqin cover penuh / parsial) menggunakan algoritma ketat dari juz_starts
- [x] Read-only untuk wali
- [x] Embed peta di Detail Santri Admin
- [x] Embed peta di Pratinjau Rapor Ustadz
- [x] Halaman Peta Hafalan 30 Juz untuk Kepala Tahfidz
- [x] Komponen Peta Juz dibangun ulang dari komponen lama

## Sprint 7 — Murajaah Bulanan
- [x] Daftar murajaah bulanan + filter bulan (derived dari setoran murajaah)
- [x] Tandai sudah murajaah (via form setoran murajaah)
- [x] Perbaiki modal Rincian Juz (P1 warisan Sprint 6)
- [x] Hapus tombol "Kirim Pengingat Masal" yang rusak
- [x] Perbaiki "Tandai Sudah" (prefill jenis murajaah di form setoran)

## Sprint 8 — PDF & Penyempurnaan
- [ ] Export Rapor Hafalan PDF (Admin, full history)
- [ ] Export Sertifikat Khatam PDF (Admin, tanpa nomor/tanggal, nama penandatangan)
- [ ] Dark mode? (dirahalkan) — validasi aksesibilitas & responsif

---

Konvensi commit: `feat(...)` untuk fitur, `bug:` untuk perbaikan, `chore:` untuk pemeliharaan.
Seed dev: admin/ustadz/kepala/wali, password `password`.
