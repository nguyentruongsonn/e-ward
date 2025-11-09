<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OutstandingServiceController;
use App\Http\Controllers\LichHenController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ProfileController;
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
            \Mail::to($to)->send(new \App\Mail\OtpCodeMail('123456'));
            return 'Đã gửi mail test tới ' . $to;
        } catch (\Throwable $e) {
            return 'Gửi mail thất bại: ' . $e->getMessage();
        }
    });
}
Route::view('/404', 'pages.404')->name('404');
Route::view('/admin/login', 'admin.login')->name('admin.login');
Route::view('/appointment/{id}', 'pages.appointment')->name('appointment');



Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
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
