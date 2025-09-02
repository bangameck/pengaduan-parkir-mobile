<?php

use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Auth\ForgotPasswordOtpController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Field\FieldReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicReportController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Route::get('/', function () {
//     return view('welcome');
// });

// routes/web.php

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('stream/{path}', function ($path) {
    // Pastikan path aman
    if (! Storage::disk('public')->exists($path) || Str::contains($path, '..')) {
        abort(404);
    }
    // Langsung redirect ke URL file yang sebenarnya di folder public
    return redirect(Storage::url($path));
})->name('media.stream')->where('path', '.*');

Route::get('/laporan-publik/{status?}', [PublicReportController::class, 'index'])->name('laporan.publik');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/image', [ProfileController::class, 'destroyImage'])->name('profile.image.destroy');
});

Route::get('/admin-panel', function () {
    return '<h1>Selamat Datang di Panel Admin</h1>';
})->middleware(['auth', 'role:super-admin,admin-officer']);

// --- GRUP ROUTE UNTUK RESIDENT ---
Route::middleware(['auth', 'role:resident'])->group(function () {
    Route::get('/laporan/buat', [ReportController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [ReportController::class, 'store'])->name('laporan.store');

    // TAMBAHKAN ROUTE INI
    Route::get('/laporan-saya', function () {
        return view('resident.laporan.my-reports');
    })->name('laporan.saya');

    // Route untuk menampilkan form edit
    Route::get('/laporan/{report}/edit', [ReportController::class, 'edit'])->name('laporan.edit');
    // Route untuk menyimpan perubahan (menggunakan method PATCH)
    Route::patch('/laporan/{report}', [ReportController::class, 'update'])->name('laporan.update');
});

// Route detail bisa diakses oleh banyak role, jadi di luar grup khusus resident
Route::get('/laporan/{report}', [ReportController::class, 'show'])->name('laporan.show')->middleware(['auth']);

// --- GRUP ROUTE UNTUK ADMIN & FIELD OFFICER ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Khusus Admin Officer & Super Admin
    Route::middleware('role:super-admin,admin-officer')->group(function () {
        Route::get('/laporan', [AdminReportController::class, 'index'])->name('laporan.index');
        Route::patch('/laporan/{report}/verifikasi', [AdminReportController::class, 'verify'])->name('laporan.verify');
        Route::patch('/laporan/{report}/tolak', [AdminReportController::class, 'reject'])->name('laporan.reject');
    });

    // Khusus Field Officer & Super Admin
    Route::middleware('role:super-admin,field-officer')->group(function () {
        Route::get('/tugas-lapangan', [FieldReportController::class, 'index'])->name('tugas.index');
        Route::get('/tugas-lapangan/{report}/tindak-lanjut', [FieldReportController::class, 'createFollowUp'])->name('tugas.createFollowUp');
        Route::post('/tugas-lapangan/{report}/tindak-lanjut', [FieldReportController::class, 'storeFollowUp'])->name('tugas.storeFollowUp');
    });

});

// --- GRUP ROUTE UNTUK LUPA PASSWORD VIA OTP ---
Route::controller(ForgotPasswordOtpController::class)->group(function () {
    // Menampilkan halaman untuk memasukkan nomor HP
    Route::get('forgot-password', 'showLinkRequestForm')->name('password.request');

    // Mengirim OTP ke nomor HP
    Route::post('forgot-password', 'sendOtp')->name('password.otp.send');

    // Menampilkan halaman untuk verifikasi OTP
    Route::get('verify-otp/{user:username}', 'showOtpForm')->name('password.otp.verify');

    // Memproses verifikasi OTP
    Route::post('verify-otp', 'verifyOtp')->name('password.otp.confirm');

    // Menampilkan halaman untuk reset password baru
    Route::get('reset-password/{user:username}/{token}', 'showResetForm')->name('password.reset');

    // Menyimpan password baru
    Route::post('reset-password', [ForgotPasswordOtpController::class, 'resetPassword'])->name('password.update.via.otp');
});

Route::get('/otp/verification/{user:username}', [OtpVerificationController::class, 'show'])->name('otp.verification');
Route::post('/otp/verify', [OtpVerificationController::class, 'verify'])->name('otp.verify');
Route::get('/otp/resend/{user:username}', [OtpVerificationController::class, 'resend'])->name('otp.resend');

// Pastikan tidak ada spasi di sini
require __DIR__ . '/auth.php';
