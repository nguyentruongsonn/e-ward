<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OutstandingServiceController;
use App\Http\Controllers\SupportController;

Route::get('/outstanding-service', [OutstandingServiceController::class, 'index'])->name('outstanding-service');

Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');

Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/history', 'pages.history')->name('history');
Route::view('/404', 'pages.404')->name('404');
Route::view('/admin/login', 'admin.login')->name('admin.login');

// // Auth routes (giả sử đã cài Laravel Breeze hoặc Auth)
// Route::post('/login', [LoginController::class, 'login'])->name('login');
// Route::post('/register', [RegisterController::class, 'register'])->name('register');
// Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// hổ trợ
// Nhóm route Hỗ Trợ
Route::controller(SupportController::class)->group(function () {
    Route::get('/support/about', 'about')->name('support.about');
    Route::get('/support/terms', 'terms')->name('support.terms');
    Route::get('/support/guide', 'guide')->name('support.guide');
    Route::get('/support/notice', 'notice')->name('support.notice');
    Route::get('/support/faq', 'faq')->name('support.faq');
});

