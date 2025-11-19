<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminderMail;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Gửi email nhắc nhở cho các lịch hẹn sắp tới trong vòng 24 giờ';

    public function handle()
    {
        $this->info('Bắt đầu gửi email nhắc nhở lịch hẹn...');
        
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $in24Hours = $now->copy()->addHours(24);
        
        // Lấy các lịch hẹn trong khoảng 23-24 giờ tới (để tránh gửi nhiều lần)
        $startTime = $now->copy()->addHours(23);
        
        $appointments = DB::table('lichhen')
            ->where('trangThai', '!=', 'Hoàn thành')
            ->where('trangThai', '!=', 'Đã hủy')
            ->where('trangThai', '!=', 'Không đến')
            ->whereBetween('thoiGianHen', [$startTime, $in24Hours])
            ->whereNull('reminder_sent_at') // Chỉ gửi nếu chưa gửi
            ->get();
        
        $this->info('Tìm thấy ' . $appointments->count() . ' lịch hẹn cần gửi nhắc nhở');
        
        $sentCount = 0;
        $errorCount = 0;
        
        foreach ($appointments as $appointment) {
            try {
                // Lấy thông tin công dân và người dùng
                $congDan = DB::table('congdan')->where('IDCD', $appointment->IDCD)->first();
                if (!$congDan) {
                    $this->warn("Không tìm thấy công dân cho lịch hẹn: {$appointment->maLichHen}");
                    continue;
                }
                
                $nguoi = DB::table('nguoi')->where('IDnguoiDung', $congDan->IDnguoiDung)->first();
                if (!$nguoi || !$nguoi->email) {
                    $this->warn("Không tìm thấy email cho lịch hẹn: {$appointment->maLichHen}");
                    continue;
                }
                
                $tthc = DB::table('tthc')->where('maTTHC', $appointment->maTTHC)->first();
                
                // Gửi email
                Mail::to($nguoi->email)->send(new AppointmentReminderMail($appointment, $tthc, $nguoi));
                
                // Đánh dấu đã gửi
                DB::table('lichhen')
                    ->where('maLichHen', $appointment->maLichHen)
                    ->update(['reminder_sent_at' => $now]);
                
                $this->info("✓ Đã gửi email nhắc nhở cho: {$nguoi->email} - {$appointment->maLichHen}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("✗ Lỗi khi gửi email cho lịch hẹn {$appointment->maLichHen}: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $this->info("\nHoàn thành!");
        $this->info("Đã gửi: {$sentCount} email");
        $this->info("Lỗi: {$errorCount} email");
        
        return 0;
    }
}

