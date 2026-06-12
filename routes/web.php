<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Mail;
use App\Models\Table;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');//done

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login'); //done
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post'); //done

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');//done
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');//done
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');//done
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');//done
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);//done

Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {

    Route::get('/dashboard', function () { //done
        $tables = Table::all();
        return view('customer.dashboard', compact('tables'));
    })->name('dashboard');

    Route::get('/booking', [BookingController::class, 'index'])//done
        ->name('booking.index');

    //chuyển đến form đặt bàn
    Route::get('/booking/create/{id}', [BookingController::class, 'create']) //done
        ->name('booking.create');

    // Lưu booking và chuyển đến trang xác nhận
    Route::post('/booking/store/{id}', [BookingController::class, 'store'])//done
        ->name('booking.store');

    // Trang xác nhận thanh toán
    Route::get('/booking/confirm/{id}', [BookingController::class, 'confirm'])//done
        ->name('booking.confirm');

    // API xác nhận thanh toán
    Route::post('/booking/confirm-payment/{id}', [BookingController::class, 'confirmPayment'])
        ->name('booking.confirm-payment');
    
    Route::get('/booking/status/{id}', [BookingController::class, 'checkStatus'])
        ->name('booking.status');

    Route::get('/booking/payment-status/{id}', [BookingController::class, 'paymentStatus'])//done
        ->name('booking.payment.status');

    Route::get('/booking/booked-times', [BookingController::class, 'getBookedTimes']);//done
    
    // Xem chi tiết booking
    Route::get('/booking/detail/{id}', [BookingController::class, 'show'])//done
        ->name('booking.show');

    //chuyển đến trang profile
    Route::get("/profile", [AuthController::class, 'profile']) //done
        ->name('profile');

    //chuyển đến trang lịch sử đặt bàn
    Route::get('/history', [BookingController::class, 'history']) //done
        ->name('history');

    Route::get('/search', [BookingController::class, 'search']) //done
        ->name('search');
    
    Route::post('/booking/cancel/{id}', [BookingController::class, 'cancel'])//done
        ->name('booking.cancel');

});

Route::middleware('auth')->group(function () { //done
    Route::put('/profile', [AuthController::class, 'updateProfile'])
        ->name('profile.update');

    Route::put('/profile/password', [AuthController::class, 'changePassword'])
        ->name('profile.password');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('dashboard');//done

    Route::get('/bookings', [BookingController::class, 'adminIndex'])
        ->name('bookings');//done

    Route::get('/bookings/export', [BookingController::class, 'adminExport'])
        ->name('bookings.export');

    Route::patch('/bookings/{id}/status', [BookingController::class, 'adminUpdateStatus'])
        ->name('bookings.status');

    Route::patch('/bookings/{id}/note', [BookingController::class, 'adminUpdateNote'])
        ->name('bookings.note');

    Route::post('/bookings/{id}/resend-email', [BookingController::class, 'adminResendEmail'])
        ->name('bookings.resend-email');

    Route::get('/tables', [TableController::class, 'adminIndex'])
        ->name('tables');//done

    Route::get('/tables/create', [TableController::class, 'create'])
        ->name('tables.create');//done

    Route::post('/tables/store', [TableController::class, 'store'])
        ->name('tables.store');//done

    Route::get('/tables/{id}/edit', [TableController::class, 'edit'])
        ->name('tables.edit');//done

    Route::put('/tables/{id}', [TableController::class, 'update'])
        ->name('tables.update');//done
        
    Route::delete('/tables/{id}', [TableController::class, 'destroy'])
        ->name('tables.destroy');//done

    Route::post('/table/{id}/occupy', [TableController::class, 'occupy'])
        ->name('tables.occupy');
        
    Route::post('/table/{id}/release', [TableController::class, 'release'])
        ->name('tables.release');

    Route::get('/revenue', [RevenueController::class, 'index'])
        ->name('revenue');//done

    Route::get('/revenue/export', [RevenueController::class, 'export'])
        ->name('revenue.export');

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports');//done

    Route::get('/export', [ReportController::class, 'export'])
        ->name('reports.export');//done
});


Route::post('/webhook/sepay', [PaymentController::class, 'handle'])//done
        ->name('webhook.sepay');

Route::get('/verify-email', function () {//done
    return view('auth.verify-email');
})->name('verify.email');

Route::post('/send-otp', [AuthController::class, 'sendOtp'])//done
    ->name('send.otp');

Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])//done
    ->name('verify.otp');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot.password');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('forgot.password.post');
