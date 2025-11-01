<?php

namespace App\Console\Commands;

use App\Mail\OtpCodeMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mail {to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending email OTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $to = $this->argument('to');
        
        $this->info('Testing mail configuration...');
        $this->line('MAIL_MAILER: ' . config('mail.default'));
        $this->line('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
        $this->line('MAIL_PORT: ' . config('mail.mailers.smtp.port'));
        $this->line('MAIL_USERNAME: ' . config('mail.mailers.smtp.username'));
        $this->line('MAIL_ENCRYPTION: ' . config('mail.mailers.smtp.encryption'));
        $this->line('Sending to: ' . $to);
        $this->newLine();
        
        try {
            Mail::to($to)->send(new OtpCodeMail('123456'));
            $this->info('✅ Email sent successfully!');
            return 0;
        } catch (\Throwable $e) {
            $this->error('❌ Failed to send email!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Common fixes:');
            $this->line('1. ⚠️ QUAN TRỌNG: Phải dùng Gmail App Password (KHÔNG phải mật khẩu Gmail thường!)');
            $this->line('   → Bật 2-Step Verification trong Google Account');
            $this->line('   → Tạo App Password: https://myaccount.google.com/apppasswords');
            $this->line('   → Copy chuỗi 16 ký tự và dán vào .env MAIL_PASSWORD');
            $this->line('2. Thử đổi port nếu vẫn lỗi:');
            $this->line('   MAIL_PORT=465');
            $this->line('   MAIL_ENCRYPTION=ssl');
            $this->line('3. Sau khi sửa .env, chạy: php artisan config:clear');
            return 1;
        }
    }
}
