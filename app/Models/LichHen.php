<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LichHen extends Model
{
    use HasFactory;

    protected $table = 'lichhen';
    // PRIMARY KEY mặc định là 'id' (không cần khai báo)
    protected $fillable = [
        'maLichHen', 'IDCD', 'maTTHC', 'maQuayLamViec',
        'thoiGianHen', 'trangThai', 'checkin_token', 'checkin_time', 'soThuTu', 'reminder_sent_at'
    ];

    protected $casts = [
        'checkin_token' => 'string',
        'thoiGianHen' => 'datetime',
        'checkin_time' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lichhen) {
            // đảm bảo maLichHen unique
            do {
                $rand = random_int(1000, 9999);
                $ma = 'LH_' . ($lichhen->IDCD ?? '0') . '_' . now()->format('Ymd') . '_' . $rand;
            } while (self::where('maLichHen', $ma)->exists());

            $lichhen->maLichHen = $ma;
            $lichhen->checkin_token = (string) Str::uuid();
        });
    }

    public function congdan()
    {
        return $this->belongsTo(CongDan::class, 'IDCD');
    }

    public function tthc()
    {
        return $this->belongsTo(TTHC::class, 'maTTHC');
    }

    public function quaylamviec()
    {
        return $this->belongsTo(QuayLamViec::class, 'maQuayLamViec');
    }
}
