<?php

use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordOtpController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Field\FieldReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Leader\AssignmentController;
use App\Http\Controllers\Leader\DashboardController as LeaderDashboardController;
use App\Http\Controllers\Leader\TeamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SuperAdmin\SettingController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\TemporaryFileController;
use App\Livewire\AdminOfficer\ReportRecap;
use App\Livewire\Field\PerformanceReport;
use App\Livewire\Field\TaskList;
use App\Livewire\Leader\ReportAssignment;
use App\Livewire\Leader\TeamList;
use App\Livewire\SuperAdmin\ReportList;
use App\Livewire\SuperAdmin\UserList;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini kita mendaftarkan semua route untuk aplikasi kita.
|
*/

// --- RUTE PUBLIK (BISA DIAKSES SEMUA ORANG) ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/laporan-publik/{status?}', [PublicReportController::class, 'index'])->name('laporan.publik');
Route::get('/laporan-details/{report:report_code}', [PublicReportController::class, 'show'])->name('public.laporan.show');

// --- RUTE AUTENTIKASI (LOGIN, REGISTER, LUPA PASSWORD) ---
Route::middleware('guest')->group(function () {
    // Register
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    // Verifikasi OTP Registrasi
    Route::get('otp/verification/{user:username}', [OtpVerificationController::class, 'show'])->name('otp.verification');
    Route::post('otp/verify', [OtpVerificationController::class, 'verify'])->name('otp.verify');
    Route::get('/otp/resend/{user:username}', [OtpVerificationController::class, 'resend'])->name('otp.resend');

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Lupa Password via OTP
    Route::get('forgot-password', [ForgotPasswordOtpController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordOtpController::class, 'sendOtp'])->name('password.otp.send');
    Route::get('verify-otp/{user:username}', [ForgotPasswordOtpController::class, 'showOtpForm'])->name('password.otp.verify');
    Route::post('verify-otp', [ForgotPasswordOtpController::class, 'verifyOtp'])->name('password.otp.confirm');
    Route::get('reset-password/{user:username}/{token}', [ForgotPasswordOtpController::class, 'showResetForm'])->name('password.reset');

    // !!! PERUBAHAN DI SINI !!!
    // Mengubah nama rute agar tidak konflik dengan rute update password di profil
    Route::post('reset-password', [ForgotPasswordOtpController::class, 'resetPassword'])->name('password.update.via.otp');
});

// --- RUTE YANG MEMBUTUHKAN LOGIN ---
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard Utama (Akan memilah berdasarkan role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil (Edit, Update, Hapus)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/image', [ProfileController::class, 'destroyImage'])->name('profile.image.destroy');
    // Ini adalah rute 'password.update' yang asli untuk pengguna yang sudah login
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // --- RUTE KHUSUS RESIDENT ---
    Route::middleware('role:resident')->group(function () {
        Route::get('/laporan/buat', [ReportController::class, 'create'])->name('laporan.create');
        Route::post('/laporan', [ReportController::class, 'store'])->name('laporan.store');
        Route::get('/laporan-saya', function () {
            return view('resident.laporan.my-reports');
        })->name('laporan.saya');
        Route::get('/laporan/{report}', [ReportController::class, 'show'])->name('laporan.show');
        Route::get('/laporan/{report}/edit', [ReportController::class, 'edit'])->name('laporan.edit');
        Route::patch('/laporan/{report}', [ReportController::class, 'update'])->name('laporan.update');
    });

    // --- RUTE KHUSUS ADMIN OFFICER ---
    Route::middleware('role:admin-officer')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/laporan', [AdminReportController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/{report}/verify', [AdminReportController::class, 'verify'])->name('laporan.verify');
        Route::post('/laporan/{report}/reject', [AdminReportController::class, 'reject'])->name('laporan.reject');
        Route::get('/tugas-lapangan', [FieldReportController::class, 'index'])->name('tugas.index');
        Route::get('/laporan/create', [AdminReportController::class, 'create'])->name('laporan.create');
        Route::post('/laporan', [AdminReportController::class, 'store'])->name('laporan.store');
        Route::get('/rekap-laporan', ReportRecap::class)->name('laporan.rekap');
    });

    // --- RUTE KHUSUS FIELD OFFICER ---
    Route::middleware('role:field-officer')->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/tugas', TaskList::class)->name('tugas.index');
        Route::get('/tugas/{report}/tindak-lanjut', [FieldReportController::class, 'createFollowUp'])->name('tugas.createFollowUp');
        Route::post('/tugas/{report}/tindak-lanjut', [FieldReportController::class, 'storeFollowUp'])->name('tugas.storeFollowUp');
        Route::get('/kinerja', PerformanceReport::class)->name('kinerja.index');
    });

    // --- RUTE KHUSUS SUPER ADMIN ---
    Route::middleware('role:super-admin')
        ->prefix('super-admin')
        ->name('super-admin.')
        ->group(function () {

            // Route awal untuk dashboard Super Admin
            Route::get('/dashboard', function () {
                return 'Halaman Dashboard Super Admin'; // Nanti kita buat view-nya
            })->name('dashboard');

            // Route::resource('users', UserController::class);
            Route::get('/users', UserList::class)->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
            Route::get('/reports', ReportList::class)->name('reports.index');
        });

    // --- RUTE KHUSUS LEADER ---
    Route::middleware('role:leader')->prefix('leader')->name('leader.')->group(function () {
        Route::get('/manajemen-tim', TeamList::class)->name('team.management');
        Route::get('/dashboard', [LeaderDashboardController::class, 'index'])->name('dashboard');
        Route::get('/team/{user}', [TeamController::class, 'show'])->name('team.show');
        Route::get('/penugasan', ReportAssignment::class)->name('team.assignment');
        Route::get('/penugasan/{report}/tugaskan', [AssignmentController::class, 'create'])->name('assignment.create');
        Route::post('/penugasan/{report}/tugaskan', [AssignmentController::class, 'store'])->name('assignment.store');

    });
});

// Route untuk streaming media (video/gambar)
Route::get('stream/{path}', function ($path) {
    // Pastikan path aman
    if (! Storage::disk('public')->exists($path) || Str::contains($path, '..')) {
        abort(404);
    }
    // Langsung redirect ke URL file yang sebenarnya di folder public
    return redirect(Storage::url($path));
})->name('media.stream')->where('path', '.*');

// Routes for FilePond temporary uploads
Route::post('/upload', [TemporaryFileController::class, 'store'])->name('temp.upload');
Route::delete('/revert', [TemporaryFileController::class, 'destroy'])->name('temp.revert');

//details laporan public
// Route::get('/laporan/{report:report_code}', [App\Http\Controllers\PublicReportController::class, 'show'])->name('public.laporan.show');
