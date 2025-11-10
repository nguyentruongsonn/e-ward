<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OutstandingServiceController;
use App\Http\Controllers\LichHenController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PaymentController;

// ...existing code...


Route::get('/outstanding-service', [OutstandingServiceController::class, 'index'])->name('outstanding-service');
Route::get('/outstanding-service/{id}', [OutstandingServiceController::class, 'show'])->name('outstanding-service.show');

Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');

Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/chatbot', 'pages.chatbot')->name('chatbot');
Route::view('/history', 'pages.history')->name('history');
Route::view('/register', 'pages.register')->name('register');
Route::post('/register', [RegisterController::class, 'submit'])->name('register.submit');
Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('register.otp.show');
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('register.otp.verify');
Route::post('/resend-otp', [RegisterController::class, 'resendOtp'])->name('register.otp.resend');

// Login routes
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Profile routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/services/load-more', [ProfileController::class, 'loadMoreServices'])
        ->name('profile.services.load-more');
    Route::get('/profile/identity', [ProfileController::class, 'identityInfo'])->name('profile.identity');
    // Ho so (hoso xuly) detail API for modal
    Route::get('/profile/hoso/{maHSXL}', [ProfileController::class, 'showHoSo'])
        ->name('profile.hoso.show');
    // Payment history
    Route::get('/profile/payments', [ProfileController::class, 'payments'])
        ->name('profile.payments');
    // Notifications
    Route::get('/profile/notifications', [ProfileController::class, 'notifications'])
        ->name('profile.notifications');
    Route::get('/profile/notifications/load-more', [ProfileController::class, 'loadMoreNotifications'])
        ->name('profile.notifications.load-more');
    Route::post('/profile/notifications/{id}/mark-read', [ProfileController::class, 'markNotificationAsRead'])
        ->name('profile.notifications.mark-read');
    Route::post('/profile/notifications/{id}/detail', [ProfileController::class, 'getNotificationDetail'])
        ->name('profile.notifications.detail');
    // Password change
    Route::get('/profile/password-change', [ProfileController::class, 'showPasswordChangeForm'])
        ->name('profile.password-change');
    Route::post('/profile/password-change', [ProfileController::class, 'requestPasswordChange'])
        ->name('profile.password-change.request');
    Route::get('/profile/password-change/verify', [ProfileController::class, 'showVerifyOtpForm'])
        ->name('profile.password-change.verify');
    Route::post('/profile/password-change/verify', [ProfileController::class, 'verifyPasswordChangeOtp'])
        ->name('profile.password-change.verify.submit');
    Route::post('/profile/password-change/resend-otp', [ProfileController::class, 'resendPasswordChangeOtp'])
        ->name('profile.password-change.resend-otp');
});

// Dev-only: quick mail test endpoint
if (app()->environment('local')) {
    Route::get('/_mail-test', function() {
        $to = request('to');
        if (!$to) {
            return 'Thiếu ?to=email@example.com';
        }
        try {
            Mail::to($to)->send(new \App\Mail\OtpCodeMail('123456'));
            return 'Đã gửi mail test tới ' . $to;
        } catch (\Throwable $e) {
            return 'Gửi mail thất bại: ' . $e->getMessage();
        }
    });
}
Route::view('/404', 'pages.404')->name('404');
Route::view('/admin/login', 'admin.login')->name('admin.login');

// Đặt lịch nộp hồ sơ
Route::middleware('auth')->group(function () {
    Route::get('/appointment/{id}', [LichHenController::class, 'show'])->name('appointment');
    Route::post('/appointment/{id}', [LichHenController::class, 'store'])->name('appointment.store');
    Route::get('/appointment/{id}/available-slots', [LichHenController::class, 'getAvailableSlots'])->name('appointment.available-slots');
});

// Check-in lịch hẹn (không cần auth vì dùng token)
Route::get('/appointment/checkin/{token}', [LichHenController::class, 'checkin'])->name('appointment.checkin');
Route::post('/appointment/checkin/{token}', [LichHenController::class, 'processCheckin'])->name('appointment.checkin.process');



Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

// Nộp hồ sơ trực tuyến (yêu cầu đăng nhập)
Route::middleware('auth')->group(function () {
    Route::get('/nop-ho-so/{maTTHC}', [SubmitController::class, 'showByTTHC'])->name('nop-ho-so.show');
    Route::post('/nop-ho-so/{maTTHC}', [SubmitController::class, 'submitApi'])->name('nop-ho-so.submit');
    Route::post('/nop-ho-so/payment/save', [SubmitController::class, 'savePaymentHistory'])->name('nop-ho-so.payment.save');
});
// // Auth routes (giả sử đã cài Laravel Breeze hoặc Auth)
// Route::post('/login', [LoginController::class, 'login'])->name('login');
// Route::post('/register', [RegisterController::class, 'register'])->name('register');
// Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// hổ trợ
Route::controller(SupportController::class)->group(function () {
    Route::get('/support/about', 'about')->name('support.about');
    Route::get('/support/terms', 'terms')->name('support.terms');
    Route::get('/support/guide', 'guide')->name('support.guide');
    Route::get('/support/notice', 'notice')->name('support.notice');
    Route::get('/support/faq', 'faq')->name('support.faq');
});
Route::post('/vnpay_payment',[PaymentController::class,'vnpay_payment']);
Route::get('/vnpay_return',[PaymentController::class,'vnpay_return'])->name('vnpay.return');
Route::view('form','pages.form')->name('form');
