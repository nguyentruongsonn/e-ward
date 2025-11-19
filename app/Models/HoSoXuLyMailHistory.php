<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSoXuLyMailHistory extends Model
{
    use HasFactory;

    protected $table = 'hosoxuly_mail_history';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'maHSXL',
        'direction', // 'outgoing' hoặc 'incoming'
        'sender_type', // 'admin' hoặc 'citizen'
        'loai_mail',
        'subject',
        'content',
        'email',
        'sent_at',
        'sent_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function hoso()
    {
        return $this->belongsTo(HoSoXuLy::class, 'maHSXL', 'maHSXL');
    }
}
