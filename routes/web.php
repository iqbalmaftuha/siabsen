<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PegawaiController;
use Illuminate\Support\Facades\Auth;


// Route pertama untuk welcome (bisa diakses tanpa login)
Route::get('/', function () {
    // Jika pengguna sudah login, redirect ke dashboard berdasarkan role
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'admin') {
            return redirect()->route('dashboardadmin');
        } elseif ($role === 'pegawai') {
            return redirect()->route('dashboard');
        }
    }
    // Jika belum login, tampilkan halaman welcome
    return view('welcome');
});

// Halaman login hanya untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route logout dan dashboard hanya untuk user yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin dashboard route
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboardadmin', [AdminController::class, 'dashboardAdmin'])->name('dashboardadmin');
        //Data Pegawai
        Route::get('/datapegawai', [AdminController::class, 'dataPegawai'])->name('data.pegawai');
        Route::post('/datapegawai/store', [AdminController::class, 'storePegawai'])->name('datapegawai.store');
        Route::post('/datapegawai/edit', [AdminController::class, 'editPegawai'])->name('datapegawai.edit');
        Route::post('/datapegawai/update/{nik}', [AdminController::class, 'updatePegawai'])->name('datapegawai.update');
        Route::delete('/datapegawai/delete/{nik}', [AdminController::class, 'deletePegawai'])->name('datapegawai.delete');
        //Konfigurasi
        Route::get('/konfigurasi', [AdminController::class, 'konfiglok'])->name('konigurasi.lokasi');
        Route::post('/konfigurasi/update', [AdminController::class, 'updateKonfiglok'])->name('konfigurasi.update');
        //Pengajuan Izin
        Route::get('/pengajuanizin', [AdminController::class, 'pengajuanIzin'])->name('pengajuan.izin');
        Route::put('/pengajuanizin/update', [AdminController::class, 'updatePengajuanizin'])->name('pengajuanizin.update');
        Route::get('/pengajuanizin/batalkan/{id}', [AdminController::class, 'batalPengajuanizin'])->name('pengajuanizin.batal');
        Route::delete('/pengajuanizin/delete/{id}', [AdminController::class, 'deletePengajuanizin'])->name('pengajuanizin.delete');
        //Monitoring Absensi
        Route::get('/monitoringabsensi', [AdminController::class, 'monitoringAbsen'])->name('monitoring.absen');
        Route::put('/monitoringabsensi/update', [AdminController::class, 'updatemonitoringAbsen'])->name('monitoringabsen.update');
        Route::delete('/monitoringabsen/delete/{id}', [AdminController::class, 'deletemonitoringAbsen'])->name('monitoringabsen.delete');
        // Rekap Absensi
        Route::get('/rekapabsenpegawai', [AdminController::class, 'rekapabsenPegawai'])->name('rekapabsen.pegawai');
        Route::post('/rekapabsenpegawai/pegawai', [AdminController::class, 'rekapabsenpegawaiLaporan'])->name('rekapabsenpegawai.laporan');
        Route::get('/rekapabsen', [AdminController::class, 'rekapAbsen'])->name('rekap.absen');
        Route::post('/rekapabsen/pegawai', [AdminController::class, 'rekapabsenLaporan'])->name('rekapabsen.laporan');
        // Profil 
        Route::get('/profiladmin', [AdminController::class, 'profilAdmin'])->name('profiladmin');
        Route::put('/profiladmin/update', [AdminController::class, 'updateprofileAdmin'])->name('profileadmin.update');
    });

    // Pegawai dashboard route
    Route::middleware('role:pegawai')->group(function () {
        Route::get('/dashboard', [PegawaiController::class, 'dashboard'])->name('dashboard');
        Route::get('/absen', [PegawaiController::class, 'absen'])->name('absen');
        Route::post('/absensi/store', [PegawaiController::class, 'storeAbsen'])->name('absensi.store');
        Route::get('/izin', [PegawaiController::class, 'izin'])->name('izin');
        Route::post('/izin/store', [PegawaiController::class, 'storeIzin'])->name('pegawai.storeIzin');
        Route::get('/histori', [PegawaiController::class, 'histori'])->name('histori');
        Route::get('/profil', [PegawaiController::class, 'profil'])->name('profil');
        Route::put('/update-profile', [PegawaiController::class, 'updateProfile'])->name('profile.update');

    });
});
