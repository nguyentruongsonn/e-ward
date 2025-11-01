<?php
/**
 * Script để kiểm tra cấu hình mail
 * Chạy: php check-mail-config.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== KIỂM TRA CẤU HÌNH MAIL ===\n\n";

echo "MAIL_MAILER: " . env('MAIL_MAILER', 'CHƯA SET') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST', 'CHƯA SET') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT', 'CHƯA SET') . "\n";
echo "MAIL_USERNAME: " . env('MAIL_USERNAME', 'CHƯA SET') . "\n";
echo "MAIL_PASSWORD: " . (env('MAIL_PASSWORD') ? '***ĐÃ SET***' : 'CHƯA SET') . "\n";
echo "MAIL_ENCRYPTION: " . (env('MAIL_ENCRYPTION') ?: 'CHƯA SET (QUAN TRỌNG!)') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS', 'CHƯA SET') . "\n";

echo "\n=== CẤU HÌNH TỪ CONFIG ===\n\n";
echo "config('mail.default'): " . config('mail.default') . "\n";
echo "config('mail.mailers.smtp.host'): " . config('mail.mailers.smtp.host') . "\n";
echo "config('mail.mailers.smtp.port'): " . config('mail.mailers.smtp.port') . "\n";
echo "config('mail.mailers.smtp.username'): " . config('mail.mailers.smtp.username') . "\n";
echo "config('mail.mailers.smtp.encryption'): " . (config('mail.mailers.smtp.encryption') ?: 'NULL (SẼ LỖI!)') . "\n";

echo "\n=== HƯỚNG DẪN SỬA ===\n\n";
echo "1. Mở file .env\n";
echo "2. Đảm bảo có đầy đủ:\n";
echo "   MAIL_MAILER=smtp\n";
echo "   MAIL_HOST=smtp.gmail.com\n";
echo "   MAIL_PORT=587\n";
echo "   MAIL_USERNAME=phamtrungnghia15082003@gmail.com\n";
echo "   MAIL_PASSWORD=APP_PASSWORD_16_KY_TU (App Password, KHÔNG phải mật khẩu Gmail)\n";
echo "   MAIL_ENCRYPTION=tls  ← QUAN TRỌNG!\n";
echo "   MAIL_FROM_ADDRESS=phamtrungnghia15082003@gmail.com\n";
echo "   MAIL_FROM_NAME=\"E-Ward\"\n\n";
echo "3. Sau khi sửa, chạy: php artisan config:clear\n";
echo "4. Test lại: php artisan test:mail phamtrungnghia15082003@gmail.com\n\n";

