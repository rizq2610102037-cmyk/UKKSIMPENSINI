<?php

use App\Models\TipeKamar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Reservasi\DashboardController as ReservasiDashboardController;

// Landing Page
Route::get('/', function () {
    $tipeKamar = TipeKamar::all();
    return view('index', compact('tipeKamar'));
})->name('index');

// Fallback Dashboard Route - Redirect berdasarkan role
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->role === 'admin') {
        return redirect('/admin');
    } elseif ($user->role === 'reservasi') {
        return redirect()->route('reservasi.dashboard');
    } else {
        return redirect()->route('user.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

// User Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');

    // User checkout sendiri (self-checkout dari dashboard)
    Route::post('/dashboard/checkout/{id}', [DashboardController::class, 'checkout'])->name('user.selfCheckout');

    // User cancel booking sendiri
    Route::post('/user/dashboard/cancel/{id}', [DashboardController::class, 'cancelBooking'])->name('user.cancelBooking');

    // Booking routes (proses pemesanan kamar)
    Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/checkout/{id}', [BookingController::class, 'checkout'])->name('booking.checkout');
    Route::post('/booking/check-payment', [BookingController::class, 'checkPaymentStatus'])->name('booking.checkPayment');

    // Voucher routes
    Route::get('/booking/vouchers', [BookingController::class, 'getUserVouchers'])->name('booking.vouchers');
    Route::post('/booking/validate-voucher', [BookingController::class, 'validateVoucher'])->name('booking.validateVoucher');
    
    Route::get('/booking/receipt/{orderId}', [BookingController::class, 'receipt'])->name('booking.receipt');

    Route::get('/thank-you', function () {
        return view('user.thankyou');
    })->name('thankyou');
});

// Route callback Midtrans (TANPA auth middleware)
Route::post('/booking/callback', [BookingController::class, 'callback'])->name('booking.callback');

// Reservasi Routes
Route::middleware(['auth'])->prefix('reservasi')->group(function () {
    Route::get('/dashboard', [ReservasiController::class, 'dashboard'])->name('reservasi.dashboard');
    Route::post('/check-in/{id}', [ReservasiController::class, 'checkIn'])->name('reservasi.checkin');
    Route::post('/check-out/{id}', [ReservasiController::class, 'checkOut'])->name('reservasi.checkout');
    Route::post('/add-denda/{id}', [ReservasiController::class, 'addDenda'])->name('reservasi.addDenda');
    Route::get('/detail/{id}', [ReservasiController::class, 'getDetail'])->name('reservasi.detail');
    Route::post('/booking/available-rooms', [BookingController::class, 'getAvailableRooms'])->name('booking.availableRooms');

    // Booking Offline Tamu - Reservasi
    Route::get('/booking-offline', [ReservasiController::class, 'bookingOfflineForm'])->name('reservasi.bookingOffline');
    Route::post('/booking-offline/check-availability', [ReservasiController::class, 'checkAvailability'])->name('reservasi.checkAvailability');
    Route::post('/booking-offline/store', [ReservasiController::class, 'storeOfflineBooking'])->name('reservasi.storeOfflineBooking');
    Route::get('/receipt/{id}', [ReservasiController::class, 'showReceipt'])->name('reservasi.receipt'); // ← TAMBAHAN INI
});

// Profile Routes (untuk semua role kecuali admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
