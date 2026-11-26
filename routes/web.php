<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Password Reset Routes (admin-mediated, no email)
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', [PasswordResetController::class, 'showRequest'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendRequest'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $unassignedSantri = \App\Models\Santri::where(function ($query) {
            $query->whereNull('wali_id')->orWhereNull('kelompok_id');
        })->count();

        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
            'pageTitle' => 'Dashboard',
            'totalSantri' => \App\Models\Santri::count(),
            'totalUstadz' => \App\Models\Ustadz::count(),
            'totalWali' => \App\Models\Wali::count(),
            'totalKelompok' => \App\Models\Kelompok::count(),
            'totalAngkatan' => \App\Models\Angkatan::count(),
            'activeAngkatan' => \App\Models\Angkatan::where('status_aktif', true)->count(),
            'unassignedSantri' => $unassignedSantri,
            'unassignedKelompok' => \App\Models\Kelompok::whereNull('ustadz_id')->count(),
        ]);
    })->name('dashboard');

    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

    Route::resource('santris', \App\Http\Controllers\Admin\SantriController::class);

    // Sprint 9: Reset Password (admin-mediated) — without email
    Route::get('/password-requests', [\App\Http\Controllers\Admin\PasswordResetController::class, 'index'])->name('password-requests.index');
    Route::post('/password-requests/{user}/issue', [\App\Http\Controllers\Admin\PasswordResetController::class, 'issue'])->name('password-requests.issue');
    Route::delete('/password-requests/{user}', [\App\Http\Controllers\Admin\PasswordResetController::class, 'destroy'])->name('password-requests.destroy');
    Route::resource('walis', \App\Http\Controllers\Admin\WaliController::class);
    Route::resource('ustadzs', \App\Http\Controllers\Admin\UstadzController::class);
    Route::resource('kelompoks', \App\Http\Controllers\Admin\KelompokController::class);
    Route::resource('angkatans', \App\Http\Controllers\Admin\AngkatanController::class);
});

/*
|--------------------------------------------------------------------------
| Ustadz Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:ustadz'])->prefix('ustadz')->name('ustadz.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Ustadz\DashboardController::class, 'index'])->name('dashboard');

    Route::post('dashboard/catatan', [\App\Http\Controllers\Ustadz\DashboardController::class, 'storeNote'])->name('dashboard.catatan');
    Route::post('pesan-wali/{message}/bacakan', [\App\Http\Controllers\Ustadz\DashboardController::class, 'markPesanDibacakan'])->name('pesan-wali.bacakan');
    Route::get('setorans/sukses/{setoran}', [\App\Http\Controllers\Ustadz\SetoranController::class, 'sukses'])->name('setorans.sukses');
    Route::resource('setorans', \App\Http\Controllers\Ustadz\SetoranController::class)->whereNumber('setoran');
    Route::resource('absensis', \App\Http\Controllers\Ustadz\AbsensiController::class)->only(['index', 'create', 'store']);
    Route::get('absensi/rekap', [\App\Http\Controllers\Ustadz\AbsensiController::class, 'rekap'])->name('absensi.rekap');
    Route::get('rekap', [\App\Http\Controllers\Ustadz\RekapController::class, 'index'])->name('rekap.index');
    Route::get('rekap/export/csv', [\App\Http\Controllers\Ustadz\RekapController::class, 'exportCsv'])->name('rekap.export.csv');
    Route::get('rekap/export/pdf', [\App\Http\Controllers\Ustadz\RekapController::class, 'exportPdf'])->name('rekap.export.pdf');
    Route::get('rapor', [\App\Http\Controllers\Ustadz\RaporController::class, 'index'])->name('rapor.index');
    Route::get('rapor/pdf', [\App\Http\Controllers\Ustadz\RaporController::class, 'exportPdf'])->name('rapor.pdf');
    Route::get('murajaahs', [\App\Http\Controllers\Ustadz\MurajaahController::class, 'index'])->name('murajaahs.index');
    Route::get('murajaahs/export-pdf', [\App\Http\Controllers\Ustadz\MurajaahController::class, 'exportPdf'])->name('murajaahs.export-pdf');
    Route::post('murajaahs', [\App\Http\Controllers\Ustadz\MurajaahController::class, 'store'])->name('murajaahs.store');
});

/*
|--------------------------------------------------------------------------
| Kepala Tahfidz Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kepala_tahfidz'])->prefix('kepala')->name('kepala.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Kepala\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/juz-distribution', [\App\Http\Controllers\Kepala\DashboardController::class, 'juzDistribution'])->name('dashboard.juz-distribution');
    Route::get('/peta-hafalan', [\App\Http\Controllers\Kepala\PetaHafalanController::class, 'index'])->name('peta-hafalan');
});

/*
|--------------------------------------------------------------------------
| Wali Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:wali'])->prefix('wali')->name('wali.')->group(function () {
    Route::get('/mutabaah/{santri?}', [\App\Http\Controllers\Wali\DashboardController::class, 'index'])->name('mutabaah');
    Route::get('/riwayat/{santri?}', [\App\Http\Controllers\Wali\DashboardController::class, 'riwayat'])->name('riwayat');
    Route::get('/peta-juz/{santri?}', [\App\Http\Controllers\Wali\DashboardController::class, 'petaJuz'])->name('peta-juz');
    Route::post('/santri/{santri}/doa', [\App\Http\Controllers\Wali\DashboardController::class, 'storeDoa'])->name('doa.store');

    Route::get('/profile', function () {
        return view('wali.profile', ['title' => 'Profil Wali']);
    })->name('profile');
});

/*
|--------------------------------------------------------------------------
| Shared Routes (Ustadz, Kepala, Wali)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:ustadz|kepala_tahfidz|wali'])->group(function () {
    // Sprint 6: Peta Hafalan 30 Juz
    // Route::get('/peta-juz/{santri?}', [\App\Http\Controllers\Shared\PetaJuzController::class, 'index'])->name('peta-juz.index');
    // Route::get('/murajaah/bulanan', [\App\Http\Controllers\Shared\MurajaahController::class, 'index'])->name('murajaah.index');
});

/*
|--------------------------------------------------------------------------
| PDF Export Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Sprint 8: PDF Export
    Route::get('/santri/{santri}/rapor/pdf', [\App\Http\Controllers\Admin\SantriController::class, 'exportRaporPdf'])->name('santris.rapor.pdf');
    Route::get('/santri/{santri}/sertifikat/pdf', [\App\Http\Controllers\Admin\SantriController::class, 'exportSertifikatPdf'])->name('santris.sertifikat.pdf');
});