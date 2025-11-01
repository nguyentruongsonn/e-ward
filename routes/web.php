<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OutstandingServiceController;
use App\Http\Controllers\LichHenController;
use App\Http\Controllers\ChatController;
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
