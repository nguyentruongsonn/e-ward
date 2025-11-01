<?php

use Illuminate\Support\Facades\Route;
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
Route::view('/404', 'pages.404')->name('404');
Route::view('/admin/login', 'admin.login')->name('admin.login');
Route::view('/appointment/{id}', 'pages.appointment')->name('appointment');



Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
// // Auth routes (giả sử đã cài Laravel Breeze hoặc Auth)
// Route::post('/login', [LoginController::class, 'login'])->name('login');
// Route::post('/register', [RegisterController::class, 'register'])->name('register');
// Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
