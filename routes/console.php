<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule gửi email nhắc nhở lịch hẹn mỗi giờ
Schedule::command('appointments:send-reminders')
    ->hourly()
    ->withoutOverlapping();

// Schedule kiểm tra email reply mỗi 15 phút
Schedule::command('email:check-replies')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
