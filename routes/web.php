<?php

use App\Http\Controllers\Auth\LoginController;
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
| Password Reset Routes
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', function () {
    return view('auth.forgot-password', ['title' => 'Lupa Password']);
})->name('password.request');

Route::post('/forgot-password', function () {
    return back()->with('success', 'Link reset password dikirim ke email.');
})->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['title' => 'Reset Password', 'token' => $token]);
})->name('password.reset');

Route::post('/reset-password', function () {
    return redirect()->route('login')->with('success', 'Password berhasil direset.');
})->name('password.update');

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

    Route::get('setorans/validasi', [\App\Http\Controllers\Ustadz\SetoranController::class, 'validasi'])->name('setorans.validasi');
    Route::post('dashboard/catatan', [\App\Http\Controllers\Ustadz\DashboardController::class, 'storeNote'])->name('dashboard.catatan');
    Route::get('setorans/sukses/{setoran}', [\App\Http\Controllers\Ustadz\SetoranController::class, 'sukses'])->name('setorans.sukses');
    Route::resource('setorans', \App\Http\Controllers\Ustadz\SetoranController::class)->whereNumber('setoran');
    Route::patch('setorans/{setoran}/approve', [\App\Http\Controllers\Ustadz\SetoranController::class, 'approve'])->name('setorans.approve');
    Route::patch('setorans/{setoran}/reject', [\App\Http\Controllers\Ustadz\SetoranController::class, 'reject'])->name('setorans.reject');

    Route::resource('absensis', \App\Http\Controllers\Ustadz\AbsensiController::class)->only(['index', 'create', 'store']);
    Route::get('absensi/rekap', [\App\Http\Controllers\Ustadz\AbsensiController::class, 'rekap'])->name('absensi.rekap');
    Route::get('rekap', [\App\Http\Controllers\Ustadz\RekapController::class, 'index'])->name('rekap.index');
    Route::get('rekap/export/csv', [\App\Http\Controllers\Ustadz\RekapController::class, 'exportCsv'])->name('rekap.export.csv');
    Route::get('rekap/export/pdf', [\App\Http\Controllers\Ustadz\RekapController::class, 'exportPdf'])->name('rekap.export.pdf');
    Route::get('rapor', [\App\Http\Controllers\Ustadz\RaporController::class, 'index'])->name('rapor.index');
    Route::get('rapor/pdf', [\App\Http\Controllers\Ustadz\RaporController::class, 'exportPdf'])->name('rapor.pdf');
    Route::get('murajaahs', [\App\Http\Controllers\Ustadz\MurajaahController::class, 'index'])->name('murajaahs.index');
    Route::post('murajaahs', [\App\Http\Controllers\Ustadz\MurajaahController::class, 'store'])->name('murajaahs.store');
});

/*
|--------------------------------------------------------------------------
| Kepala Tahfidz Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kepala_tahfidz'])->prefix('kepala')->name('kepala.')->group(function () {
    Route::get('/dashboard', function () {
        return view('kepala.dashboard', [
            'title' => 'Dashboard Kepala Tahfidz',
            'pageTitle' => 'Dashboard',
            'totalSantri' => \App\Models\Santri::count(),
            'totalKelompok' => \App\Models\Kelompok::count(),
            'totalSetoran' => \App\Models\Setoran::count(),
        ]);
    })->name('dashboard');

    // Sprint 4: Dasbor Kepala
    // Route::get('/peta-juz', function () {
    //     return view('kepala.peta-juz', ['title' => 'Peta Hafalan 30 Juz']);
    // })->name('peta-juz');
    // Route::get('/murajaah/bulanan', function () {
    //     return view('kepala.murajaah-bulanan', ['title' => 'Daftar Murajaah Bulanan']);
    // })->name('murajaah.bulanan');
});

/*
|--------------------------------------------------------------------------
| Wali Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:wali'])->prefix('wali')->name('wali.')->group(function () {
    Route::get('/mutabaah/{santri?}', function () {
        return view('wali.mutabaah', [
            'title' => "Mutaba'ah Anak",
            'pageTitle' => "Mutaba'ah Anak",
            'anakans' => auth()->user()->wali?->santris ?? collect(),
        ]);
    })->name('mutabaah');

    Route::get('/riwayat/{santri?}', function () {
        return view('wali.riwayat', ['title' => 'Riwayat Setoran']);
    })->name('riwayat');

    Route::get('/peta-juz/{santri?}', function () {
        return view('wali.peta-juz', ['title' => 'Peta Hafalan']);
    })->name('peta-juz');

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
Route::middleware('auth')->group(function () {
    // Sprint 8: PDF Export
    // Route::get('/santri/{santri}/rapor/pdf', [\App\Http\Controllers\Admin\SantriController::class, 'exportRaporPdf'])->name('santris.rapor.pdf');
    // Route::get('/santri/{santri}/sertifikat/pdf', [\App\Http\Controllers\Admin\SantriController::class, 'exportSertifikatPdf'])->name('santris.sertifikat.pdf');
});