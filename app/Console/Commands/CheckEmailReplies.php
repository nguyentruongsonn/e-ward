<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HoSoXuLy;
use App\Models\HoSoXuLyMailHistory;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckEmailReplies extends Command
{
    protected $signature = 'email:check-replies {--daemon : Chạy liên tục (daemon mode)} {--interval=30 : Khoảng thời gian giữa các lần kiểm tra (giây)}';
    protected $description = 'Kiểm tra email inbox để nhận email reply từ công dân (qua IMAP)';

    public function handle()
    {
        $daemon = $this->option('daemon');
        $interval = (int) $this->option('interval');
        
        if ($daemon) {
            $this->info('🚀 Bắt đầu chạy daemon mode - kiểm tra email mỗi ' . $interval . ' giây...');
            $this->info('Nhấn Ctrl+C để dừng.');
            $this->newLine();
            
            // Chạy liên tục
            while (true) {
                try {
                    $this->checkEmails();
                    
                    // Đợi interval giây trước khi check lại
                    $this->info("⏳ Đợi {$interval} giây trước lần kiểm tra tiếp theo...");
                    sleep($interval);
                    $this->newLine();
                } catch (\Exception $e) {
                    $this->error('Lỗi trong daemon: ' . $e->getMessage());
                    Log::error('Lỗi trong email daemon: ' . $e->getMessage());
                    // Đợi 5 giây trước khi thử lại
                    sleep(5);
                }
            }
        } else {
            $this->checkEmails();
        }
        
        return 0;
    }
    
    private function checkEmails()
    {
        $this->info('Bắt đầu kiểm tra email reply...');
        
        // Kiểm tra IMAP extension
        if (!function_exists('imap_open')) {
            $this->error('PHP IMAP extension chưa được bật!');
            $this->warn('Vui lòng bật extension imap trong php.ini:');
            $this->warn('1. Mở file php.ini');
            $this->warn('2. Tìm dòng: ;extension=imap');
            $this->warn('3. Bỏ dấu ; để thành: extension=imap');
            $this->warn('4. Restart Apache/XAMPP');
            $this->warn('');
            $this->warn('Hoặc sử dụng webhook endpoint: POST /webhook/email-reply');
            return 1;
        }
        
        // Lấy cấu hình IMAP từ .env
        $imapHost = env('IMAP_HOST', 'imap.gmail.com');
        $imapPort = env('IMAP_PORT', 993);
        $imapUsername = env('MAIL_USERNAME');
        $imapPassword = env('MAIL_PASSWORD');
        $imapFolder = env('IMAP_FOLDER', 'INBOX');
        
        // Xử lý mật khẩu có dấu ngoặc kép
        $imapPassword = trim($imapPassword, '"\'');
        
        if (!$imapUsername || !$imapPassword) {
            $this->error('Chưa cấu hình MAIL_USERNAME hoặc MAIL_PASSWORD trong .env');
            return 1;
        }
        
        try {
            // Kết nối IMAP
            $mailbox = '{' . $imapHost . ':' . $imapPort . '/imap/ssl}' . $imapFolder;
            $this->info("Đang kết nối đến: {$imapHost}...");
            $connection = @imap_open($mailbox, $imapUsername, $imapPassword);
            
            if (!$connection) {
                $error = imap_last_error();
                $this->error('Không thể kết nối IMAP: ' . ($error ?: 'Unknown error'));
                $this->warn('Kiểm tra lại:');
                $this->warn('- IMAP_HOST: ' . $imapHost);
                $this->warn('- IMAP_PORT: ' . $imapPort);
                $this->warn('- MAIL_USERNAME: ' . $imapUsername);
                $this->warn('- MAIL_PASSWORD: ' . (strlen($imapPassword) > 0 ? '***ĐÃ SET***' : 'CHƯA SET'));
                $this->warn('');
                $this->warn('Với Gmail, cần:');
                $this->warn('1. Bật "Less secure app access" hoặc tạo App Password');
                $this->warn('2. Đảm bảo IMAP đã được bật trong Gmail settings');
                return 1;
            }
            
            $this->info('✓ Đã kết nối IMAP thành công!');
            
            // Lấy danh sách email của các công dân đã được gửi mail
            // Lấy từ cả HoSoXuLy và từ mail history (email đã được gửi đi)
            $citizenEmailsFromHoSo = HoSoXuLy::whereNotNull('email')
                ->where('email', '!=', '')
                ->distinct()
                ->pluck('email')
                ->map(function($email) {
                    return strtolower(trim($email));
                })
                ->filter()
                ->toArray();
            
            $citizenEmailsFromHistory = HoSoXuLyMailHistory::where('direction', 'outgoing')
                ->distinct()
                ->pluck('email')
                ->map(function($email) {
                    return strtolower(trim($email));
                })
                ->filter()
                ->toArray();
            
            // Merge và loại bỏ duplicate
            $citizenEmails = array_unique(array_merge($citizenEmailsFromHoSo, $citizenEmailsFromHistory));
            
            if (empty($citizenEmails)) {
                $this->warn('Không tìm thấy email công dân nào trong hệ thống');
                imap_close($connection);
                return 0;
            }
            
            $this->info('Tìm thấy ' . count($citizenEmails) . ' email công dân trong hệ thống');
            
            // Lấy danh sách subject của email đã gửi để tìm reply
            $sentSubjects = HoSoXuLyMailHistory::where('direction', 'outgoing')
                ->whereNotNull('subject')
                ->distinct()
                ->pluck('subject')
                ->map(function($subject) {
                    // Loại bỏ "Re:" nếu có để tìm reply
                    return preg_replace('/^Re:\s*/i', '', trim($subject));
                })
                ->filter()
                ->toArray();
            
            // Tìm email từ công dân - tìm trong toàn bộ inbox (30 ngày) để đảm bảo không bỏ sót
            $since = date('d-M-Y', strtotime('-30 days'));
            $this->info("Đang tìm email từ công dân từ {$since}...");
            
            // Tìm tất cả email (không chỉ UNSEEN) để đảm bảo không bỏ sót
            $emails = @imap_search($connection, 'SINCE "' . $since . '"');
            
            if (!$emails || !is_array($emails)) {
                $this->info('Không tìm thấy email nào từ ' . $since);
                imap_close($connection);
                return 0;
            }
            
            $this->info('Đang kiểm tra ' . count($emails) . ' email trong inbox...');
            
            $processed = 0;
            $skippedNoHoSo = 0;
            $skippedNotReply = 0;
            $skippedExists = 0;
            
            foreach ($emails as $emailNumber) {
                try {
                    $header = @imap_headerinfo($connection, $emailNumber);
                    if (!$header || !isset($header->from[0])) {
                        continue;
                    }
                    $fromEmail = strtolower($header->from[0]->mailbox . '@' . $header->from[0]->host);
                    $subject = $this->decodeMimeHeader($header->subject ?? '');
                    $date = $header->date ?? now();
                
                    // Kiểm tra xem email có trong danh sách công dân không
                    if (!in_array($fromEmail, $citizenEmails)) {
                        $skippedNoHoSo++;
                        continue; // Không phải email từ công dân - bỏ qua
                    }
                
                    // Tìm hồ sơ theo email (không phân biệt hoa thường)
                    $hoSo = HoSoXuLy::whereRaw('LOWER(email) = ?', [strtolower($fromEmail)])->first();
                    
                    if (!$hoSo) {
                        $skippedNoHoSo++;
                        continue; // Không tìm thấy hồ sơ - bỏ qua
                    }
                    
                    // Kiểm tra xem có phải reply không
                    $isReply = stripos($subject, 'Re:') !== false || 
                               stripos($subject, 'RE:') !== false ||
                               stripos($subject, 'RE:') === 0 ||
                               (isset($header->in_reply_to) && $header->in_reply_to);
                    
                    // Kiểm tra xem subject có liên quan đến email đã gửi không
                    $subjectWithoutRe = preg_replace('/^Re:\s*/i', '', trim($subject));
                    $isRelatedToSent = false;
                    foreach ($sentSubjects as $sentSubject) {
                        if (stripos($subjectWithoutRe, $sentSubject) !== false || 
                            stripos($sentSubject, $subjectWithoutRe) !== false) {
                            $isRelatedToSent = true;
                            break;
                        }
                    }
                    
                    // Vẫn lưu email từ công dân dù không phải reply (có thể là email mới từ công dân)
                    
                    // Kiểm tra xem email này đã được lưu chưa (kiểm tra theo subject và thời gian gần)
                    $sentAt = Carbon::parse($date);
                    $existing = HoSoXuLyMailHistory::where('maHSXL', $hoSo->maHSXL)
                        ->where('email', $fromEmail)
                        ->where('subject', $subject)
                        ->whereBetween('sent_at', [
                            $sentAt->copy()->subMinutes(10),
                            $sentAt->copy()->addMinutes(10)
                        ])
                        ->first();
                    
                    if ($existing) {
                        $skippedExists++;
                        continue; // Đã lưu rồi
                    }
                    
                    // Lấy nội dung email với structure để parse multipart
                    $structure = @imap_fetchstructure($connection, $emailNumber);
                    $textContent = '';
                    
                    if ($structure) {
                        // Parse multipart email
                        $textContent = $this->getTextFromStructure($connection, $emailNumber, $structure);
                    }
                    
                    // Nếu không parse được, lấy body trực tiếp
                    if (empty($textContent)) {
                        $body = @imap_body($connection, $emailNumber);
                        if ($body) {
                            $textContent = $this->decodeEmailContent($body);
                        }
                    }
                    
                    if (empty($textContent)) {
                        continue;
                    }
                    
                    // Lưu vào database
                    HoSoXuLyMailHistory::create([
                        'maHSXL' => $hoSo->maHSXL,
                        'direction' => 'incoming',
                        'sender_type' => 'citizen',
                        'loai_mail' => 'lien_lac',
                        'subject' => $subject,
                        'content' => $textContent,
                        'email' => $fromEmail,
                        'sent_at' => $sentAt,
                    ]);
                    
                    $processed++;
                    $this->info("✓ Đã lưu email reply: {$subject}");
                } catch (\Exception $e) {
                    $this->warn("  - Lỗi khi xử lý email #{$emailNumber}: " . $e->getMessage());
                    continue;
                }
            }
            
            imap_close($connection);
            
            if ($processed > 0) {
                $this->info("\n✓ Hoàn thành! Đã xử lý {$processed} email reply mới.");
            } else {
                $this->info("\n✓ Hoàn thành! Không có email reply mới.");
            }
        } catch (\Exception $e) {
            $this->error('Lỗi: ' . $e->getMessage());
            Log::error('Lỗi khi check email reply: ' . $e->getMessage());
            throw $e; // Throw lại để daemon mode có thể xử lý
        }
    }
    
    /**
     * Lấy text content từ email structure (hỗ trợ multipart)
     */
    private function getTextFromStructure($connection, $emailNumber, $structure, $partNumber = '')
    {
        $textContent = '';
        
        if ($structure->type == 0) {
            // Text part
            $body = @imap_fetchbody($connection, $emailNumber, $partNumber ?: '1');
            
            if ($body) {
                // Kiểm tra encoding
                $encoding = isset($structure->encoding) ? $structure->encoding : 0;
                
                // Decode theo encoding
                switch ($encoding) {
                    case 3: // BASE64
                        $body = base64_decode($body);
                        break;
                    case 4: // QUOTED-PRINTABLE
                        $body = quoted_printable_decode($body);
                        break;
                }
                
                // Kiểm tra subtype
                $subtype = isset($structure->subtype) ? strtolower($structure->subtype) : 'plain';
                
                if ($subtype == 'html') {
                    // Loại bỏ HTML tags và decode HTML entities
                    $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $body = strip_tags($body);
                    // Loại bỏ các ký tự đặc biệt từ HTML
                    $body = preg_replace('/\s+/', ' ', $body);
                }
                
                // Decode charset nếu cần
                if (function_exists('mb_convert_encoding')) {
                    // Lấy charset từ structure parameters
                    $charset = 'UTF-8';
                    if (isset($structure->parameters)) {
                        foreach ($structure->parameters as $param) {
                            if (strtolower($param->attribute) == 'charset') {
                                $charset = $param->value;
                                break;
                            }
                        }
                    }
                    
                    if (strtoupper($charset) != 'UTF-8') {
                        $body = mb_convert_encoding($body, 'UTF-8', $charset);
                    }
                }
                
                $textContent = trim($body);
            }
        } elseif ($structure->type == 1) {
            // Multipart - duyệt qua các parts
            if (isset($structure->parts) && is_array($structure->parts)) {
                foreach ($structure->parts as $index => $part) {
                    $partNum = $partNumber ? ($partNumber . '.' . ($index + 1)) : ($index + 1);
                    $partText = $this->getTextFromStructure($connection, $emailNumber, $part, $partNum);
                    
                    // Ưu tiên text/plain hơn text/html
                    if (!empty($partText)) {
                        if (empty($textContent) || (isset($part->subtype) && strtolower($part->subtype) == 'plain')) {
                            $textContent = $partText;
                        }
                    }
                }
            }
        }
        
        return $textContent;
    }

    /**
     * Decode email content từ raw body
     */
    private function decodeEmailContent($body)
    {
        if (empty($body)) {
            return '';
        }
        
        // Loại bỏ boundary markers và headers nếu có
        $body = preg_replace('/--[a-zA-Z0-9]+--?\s*/', '', $body);
        $body = preg_replace('/Content-Type:.*?\r?\n/i', '', $body);
        $body = preg_replace('/Content-Transfer-Encoding:.*?\r?\n/i', '', $body);
        $body = preg_replace('/charset=.*?\r?\n/i', '', $body);
        
        // Tách các parts nếu là multipart (tách bằng 2 dòng trống)
        $parts = preg_split('/\r?\n\r?\n/', $body, 2);
        if (count($parts) > 1) {
            // Bỏ qua header, lấy content
            $body = $parts[1] ?? $parts[0];
        }
        
        // Thử decode base64 (kiểm tra xem có phải base64 không)
        $base64Decoded = @base64_decode($body, true);
        if ($base64Decoded !== false && strlen($base64Decoded) > 0) {
            // Kiểm tra xem decoded có hợp lệ không (không chứa quá nhiều ký tự control)
            $controlChars = preg_match_all('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $base64Decoded);
            if ($controlChars < strlen($base64Decoded) * 0.1) {
                $body = $base64Decoded;
            }
        }
        
        // Decode quoted-printable
        if (function_exists('quoted_printable_decode')) {
            $body = quoted_printable_decode($body);
        }
        
        // Loại bỏ HTML tags
        $body = strip_tags($body);
        
        // Decode HTML entities
        $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Loại bỏ các ký tự đặc biệt và normalize whitespace
        $body = preg_replace('/=\r?\n/', '', $body); // Loại bỏ soft line breaks trong quoted-printable
        $body = preg_replace('/\r\n/', "\n", $body); // Normalize line endings
        $body = preg_replace('/\n\s*\n\s*\n/', "\n\n", $body); // Loại bỏ nhiều dòng trống
        $body = preg_replace('/^>\s*/m', '', $body); // Loại bỏ quote markers (>) nếu có
        
        return trim($body);
    }
    
    /**
     * Decode MIME encoded-word header (RFC 2047)
     * Ví dụ: =?UTF-8?Q?Re:_Y=C3=AAu_c=E1=BA=A7u?= -> Re: Yêu cầu
     */
    private function decodeMimeHeader($header)
    {
        if (empty($header)) {
            return '';
        }
        
        // Sử dụng imap_mime_header_decode nếu có
        if (function_exists('imap_mime_header_decode')) {
            $decoded = imap_mime_header_decode($header);
            $result = '';
            foreach ($decoded as $part) {
                $result .= $part->text;
            }
            return $result;
        }
        
        // Fallback: decode thủ công
        // Pattern: =?charset?encoding?text?=
        $pattern = '/=\?([^?]+)\?([QB])\?([^?]+)\?=/i';
        
        return preg_replace_callback($pattern, function($matches) {
            $charset = $matches[1];
            $encoding = strtoupper($matches[2]);
            $text = $matches[3];
            
            if ($encoding == 'Q') {
                // Quoted-printable
                $text = str_replace('_', ' ', $text);
                $text = quoted_printable_decode($text);
            } elseif ($encoding == 'B') {
                // Base64
                $text = base64_decode($text);
            }
            
            // Convert charset nếu cần
            if (function_exists('mb_convert_encoding') && strtoupper($charset) != 'UTF-8') {
                $text = mb_convert_encoding($text, 'UTF-8', $charset);
            }
            
            return $text;
        }, $header);
    }
}
