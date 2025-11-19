<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HoSoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $hoSo;
    public $subject;
    public $content;
    public $loaiMail; // 'bo_sung' hoặc 'lien_lac'

    public function __construct($hoSo, $subject, $content, $loaiMail = 'lien_lac')
    {
        $this->hoSo = $hoSo;
        $this->subject = $subject;
        $this->content = $content;
        $this->loaiMail = $loaiMail;
    }

    public function build()
    {
        // Thêm Reply-To header để email reply sẽ được gửi về đúng địa chỉ
        $replyTo = config('mail.from.address');
        
        return $this->subject($this->subject)
            ->replyTo($replyTo) // Email reply sẽ được gửi về đây
            ->view('emails.hoso')
            ->with([
                'hoSo' => $this->hoSo,
                'content' => $this->content,
                'loaiMail' => $this->loaiMail,
            ]);
    }
}

