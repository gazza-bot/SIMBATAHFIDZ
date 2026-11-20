# Laporan Progres Pembangunan Aplikasi SIMBA TAHFIDZ

## Ringkasan Eksekutif
SIMBA TAHFIDZ (Sistem Informasi Mutaba'ah & Belajar Al-Qur'an) adalah aplikasi berbasis web untuk Pondok Pesantren Darut Taqwa Ponorogo yang dibangun dengan Laravel 12 dan Tailwind CSS v4. Aplikasi ini memiliki 4 peran pengguna: Admin, Ustadz, Kepala Tahfidz, dan Wali. Pengembangan mengikuti metodologi Agile dengan 8 sprint, saat ini telah menyelesaikan Sprint 0, Sprint 1, dan Sprint 2, sedang memasuki Sprint 3.

---

## Fitur yang Telah Dibangun

### 1. Sistem Autentikasi & Otorisasi (Sprint 0)
**Fungsi**: Login berbasis username/password dengan redirect otomatis ke dashboard sesuai peran.
**Pengguna**: Semua peran (Admin, Ustadz, Kepala Tahfidz, Wali).
**Input**: Username dan password.
**Proses**: Validasi kredensial, pengecekan peran, pembuatan session, redirect ke route dashboard yang sesuai.
**Output**: Session terautentikasi, akses ke fitur sesuai hak akses.
**Implementasi**:
- Controller: `app/Http/Controllers/Auth/LoginController.php` (method `show`, `login`, `logout`, `redirectToDashboard`)
- Middleware: `app/Http/Middleware/RoleMiddleware.php` (alias `role`)
- View: `resources/views/auth/login.blade.php` (extends `layouts.guest.blade.php`)
- Routes: `routes/web.php` baris 16-18
- Kredensial dev: `admin`/`ustadz`/`kepala`/`wali` dengan password `password`

### 2. Desain Sistem & Komponen UI (Sprint 0)
**Fungsi**: Design system terpusat berbasis CSS custom properties (@theme tokens) untuk konsistensi visual.
**Pengguna**: Developer & pengguna akhir (melalui UI).
**Input**: Token desain (warna, tipografi, spacing, radius, motion, breakpoint).
**Proses**: Definisi token di `resources/css/app.css`, kompilasi via Vite, penggunaan di Blade components.
**Output**: 16 komponen Blade reusable, 3 layout utama, styling konsisten tanpa hardcode.
**Implementasi**:
- Token: `resources/css/app.css` (warna primary/secondary/accent/status, font Nunito Sans/Amiri/JetBrains Mono, spacing 4px base, radius ladder, motion tokens)
- Komponenten: `resources/views/components/` (button, input, card, card-santri, avatar, badge, modal, table, dropdown, tabs, alert, pagination, breadcrumb, amalan-yaumiyah, peta-juz)
- Layout: `resources/views/layouts/app.blade.php` (authenticated shell), `guest.blade.php` (split-screen login), `print.blade.php` (PDF)

### 3. Basis Data & Seeder (Sprint 0)
**Fungsi**: Skema database 11 tabel dengan relasi lengkap dan data referensi 114 surah.
**Pengguna**: Sistem (backend), Admin (melalui CRUD).
**Input**: Migration files, SurahSeeder, DatabaseSeeder.
**Proses**: `php artisan migrate:fresh --seed` membuat tabel users, santris, walis, ustadzs, kepalas, angkatans, kelompoks, surahs, setorans, absensis, juz_starts, halaqah_notes, simaan_schedules.
**Output**: Database MySQL siap pakai dengan data development.
**Implementasi**:
- Migration: `database/migrations/` (17 file, FK order: surahs sebelum setorans)
- Seeder: `database/seeders/SurahSeeder.php` (114 surah), `DatabaseSeeder.php` (4 akun dev + relasi santri/wali/ustadz/kelompok/angkatan)
- Model: `app/Models/` (10 Eloquent models dengan accessor progress_juz, progress_percent, mutu_terakhir, is_cover_penuh)

### 4. Kelola Data Master - Admin (Sprint 1)
**Fungsi**: CRUD lengkap untuk 5 entitas master: Santri, Wali, Ustadz, Kelompok Halaqah, Angkatan.
**Pengguna**: Admin.
**Input**: Form create/edit (NIS, nama, relasi, foto, status, dll), pencarian, filter.
**Proses**: Validasi request, penyimpanan ke database dengan integritas FK, pagination, flash message.
**Output**: Daftar data terkelola, detail, form edit, hapus dengan konfirmasi.
**Implementasi**:
- Controller: `app/Http/Controllers/Admin/{Santri,Wali,Ustadz,Kelompok,Angkatan}Controller.php` (resource controller penuh)
- Views: `resources/views/admin/{santris,walis,ustadzs,kelompoks,angkatans}/{index,create,edit,show}.blade.php`
- Routes: `routes/web.php` baris 69-73 (resource routes di dalam group `admin` dengan middleware `role:admin`)
- Fitur: Search nama/NIS, filter kelompok/angkatan/status, relasi wali↔santri↔kelompok↔ustadz↔angkatan, empty/loading/error states (R-27)

### 5. Input Setoran Hafalan - Ustadz (Sprint 2)
**Fungsi**: Mencatat setoran hafalan (ziyadah/murajaah/tasmi) dengan auto-approve dan indikator cover penuh (Mutqin).
**Pengguna**: Ustadz.
**Input**: Pilih santri, tanggal, jenis, surah, juz, ayat awal-akhir, nilai (maqbul/jayyid/jayyid_jiddan/mumtaz), skor opsional, catatan.
**Proses**: Validasi rentang ayat sesuai juz & surah, cek cover penuh otomatis (ayat_awal=1 & ayat_akhir=jumlah_ayat), simpan dengan status `approved` langsung, validated_by=ustadz login, redirect ke halaman sukses.
**Output**: Record setoran tersimpan, update progres santri (progress_juz, progress_percent), halaman sukses detail, daftar setoran dengan filter jenis.
**Implementasi**:
- Controller: `app/Http/Controllers/Ustadz/SetoranController.php` (index, create, store, show, edit, update, destroy, sukses, validasi custom `buildJuzSurahMap`, `validateSetoran`)
- Views: `resources/views/ustadz/setorans/{index,create,show,edit,sukses}.blade.php` + partials `santri-context.blade.php`, `form.blade.php`
- Routes: `routes/web.php` baris 85-86 (resource `setorans` + route `setorans.sukses`)
- Model: `app/Models/Setoran.php` (accessor `is_cover_penuh`, `ayat_range`, `jumlah_ayat`, `resolveJuz`, scope ziyadah/murajaah/tasmi)
- UI: Dua kolom (kiri: konteks santri + statistik hari ini, kanan: form input), indikator Mutqin, tanpa fitur draft

### 6. Dasbor Ustadz (Sprint 2)
**Fungsi**: Ringkasan real-time halaqah: kehadiran, setoran hari ini, rata-rata mutu, feed setoran terbaru, target sima'an, catatan halaqah.
**Pengguna**: Ustadz.
**Input**: Filter tab (semua/belum/sudah setor), catatan halaqah harian.
**Proses**: Agregasi data santri kelompok, setoran hari ini, absensi, nilai mutu dikonversi ke skor, 5 setoran terbaru.
**Output**: Kartu statistik, tabel santri dengan status setoran & aksi cepat, live feed, form catatan.
**Implementasi**:
- Controller: `app/Http/Controllers/Ustadz/DashboardController.php` (index, storeNote)
- View: `resources/views/ustadz/dashboard.blade.php`
- Model: `app/Models/HalaqahNote.php` (catatan per kelompok per hari)

### 7. Absensi Kelompok - Ustadz (Sprint 3 - Sedang Dibangun)
**Fungsi**: Presensi harian per sesi (subuh/ashar/maghrib) dengan status Hadir/Izin/Sakit/Alpa dan rekap bulanan.
**Pengguna**: Ustadz.
**Input**: Tanggal, sesi, array absensi per santri (status, waktu tiba, catatan).
**Proses**: `updateOrCreate` per santri/tanggal/sesi, agregasi bulanan per santri.
**Output**: Form presensi dengan statistik real-time, tabel rekap bulanan dengan filter bulan/tahun.
**Implementasi**:
- Controller: `app/Http/Controllers/Ustadz/AbsensiController.php` (index, create, store, rekap)
- Views: `resources/views/ustadz/absensis/{index,rekap}.blade.php`
- Routes: `routes/web.php` baris 87-91
- Model: `app/Models/Absensi.php`

---

## Fitur yang Belum Dibangun (Rencana Sprint 4-8)

| Sprint | Fitur | Status |
|--------|-------|--------|
| 4 | Dasbor Kepala Tahfidz: statistik keseluruhan + Chart.js, daftar santri perlu perhatian | Pending |
| 5 | Dasbor Wali: mutaba'ah multi-anak (tab), riwayat setoran & absensi | Pending |
| 6 | Peta Hafalan 30 Juz: grid visual Mutqin/parsial, read-only untuk wali | Pending |
| 7 | Murajaah Bulanan: daftar + filter bulan, tandai sudah/belum | Pending |
| 8 | PDF Export: Rapor Hafalan & Sertifikat Khatam (dompdf), audit aksesibilitas | Pending |

---

## Arsitektur & Konvensi Teknis

- **Backend**: Laravel 12, PHP 8.2, MySQL 8.x (XAMPP)
- **Frontend**: Tailwind CSS v4 (JIT via Vite), Blade templates, Chart.js 4.x (tersedia Sprint 4)
- **Auth**: Custom username/password, 4 roles, middleware `role` alias
- **PDF**: `barryvdh/laravel-dompdf` (Sprint 8)
- **Testing**: Pest PHP ^3.8
- **Icons**: Inline SVG (aria-hidden untuk dekoratif)
- **Fonts**: Google Fonts (Nunito Sans, Amiri, JetBrains Mono)
- **Commit Convention**: `feat(scope)`, `bug:`, `chore:`, `refactor(scope)`
- **Git Remote**: `https://github.com/gazza-bot/SIMBATAHFIDZ` (branch `main`)

---

## Catatan Penting
- Semua nilai visual berasal dari `@theme` tokens di `resources/css/app.css` (tidak ada hardcode warna/font)
- RTL wajib untuk teks Arab (font-arabic + dir="rtl" + lang="ar")
- Empty/loading/error states wajib pada setiap data UI (R-27)
- Navigasi sidebar hanya menampilkan halaman yang sudah dibangun (R-24)
- Placeholder menggunakan dashed border box, bukan aset palsu (R-23)
- Mode antislop: `during` (default project)