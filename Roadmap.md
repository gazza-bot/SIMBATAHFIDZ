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
- [x] Statistik keseluruhan + grafik (Chart.js)
- [x] Daftar santri perlu perhatian
- [x] 3 charts: Progress Juz (bar), Kehadiran 30 hari (line), Mutu (doughnut)
- [x] Attention list with criteria: attendance < 75%, no murajaah > 14d, progress < 3 juz

## Sprint 5 — Dasbor Wali
- [ ] Mutaba'ah anak (tab multi-anak)
- [ ] Riwayat setoran & absensi

## Sprint 6 — Peta Hafalan 30 Juz
- [ ] Grid 30 juz (Mutqin cover penuh / parsial)
- [ ] Read-only untuk wali

## Sprint 7 — Murajaah Bulanan
- [ ] Daftar murajaah bulanan + filter bulan
- [ ] Tandai sudah/belum murajaah

## Sprint 8 — PDF & Penyempurnaan
- [ ] Export Rapor Hafalan PDF
- [ ] Export Sertifikat Khatam PDF
- [ ] Dark mode? (dirahalkan) — validasi aksesibilitas & responsif

---

Konvensi commit: `feat(...)` untuk fitur, `bug:` untuk perbaikan, `chore:` untuk pemeliharaan.
Seed dev: admin/ustadz/kepala/wali, password `password`.
