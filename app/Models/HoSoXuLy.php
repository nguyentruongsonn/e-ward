<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSoXuLy extends Model
{
    use HasFactory;

    protected $table = 'hosoxuly';
    protected $primaryKey = 'maHSXL';
    public $incrementing = false; // Primary key không phải auto-increment
    protected $keyType = 'string'; // Primary key là VARCHAR/STRING
    public $timestamps = false;

    protected $fillable = [
        'maTTHC',
        'IDCD',
        'maForm',
        'tenChuHoSo',
        'doiTuongThucHien',
        'email',
        'soDienThoai',
        'dulieu',
        'ngayTiepNhan',
        'ngayHenTra',
        'maTrangThai',
        'ngayTra',
        'hanBoSung',
        'thongTinTra',
        'lePhi',
        'hinhThuc',
        'ngayKetThucXuLy',
        'donViXuLy',
        'ghiChu',
        'last_mail_sent_at',
    ];

    protected $casts = [
        'dulieu' => 'array',
        'ngayTiepNhan' => 'date',
        'ngayHenTra' => 'date',
        'ngayTra' => 'date',
        'ngayKetThucXuLy' => 'date',
        'lePhi' => 'float',
        'last_mail_sent_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hoso) {
            // Tự động tạo mã hồ sơ nếu chưa có hoặc là "0"
            if (empty($hoso->maHSXL) || $hoso->maHSXL == '0' || !preg_match('/^HSXL_/', $hoso->maHSXL)) {
                $IDCD = $hoso->IDCD ?? 0;
                
                // Nếu IDCD = 0, cố gắng lấy từ email
                if ($IDCD == 0 && !empty($hoso->email)) {
                    $nguoi = \Illuminate\Support\Facades\DB::table('nguoi')
                        ->where('email', $hoso->email)
                        ->first();
                    
                    if ($nguoi) {
                        $congDan = \Illuminate\Support\Facades\DB::table('congdan')
                            ->where('IDnguoiDung', $nguoi->IDnguoiDung)
                            ->first();
                        
                        if ($congDan) {
                            $IDCD = $congDan->IDCD;
                        } else {
                            $IDCD = \Illuminate\Support\Facades\DB::table('congdan')->insertGetId([
                                'IDnguoiDung' => $nguoi->IDnguoiDung,
                            ]);
                        }
                        
                        // Cập nhật IDCD vào hồ sơ
                        $hoso->IDCD = $IDCD;
                    }
                }
                
                // Lấy ngày từ ngayTiepNhan hoặc dùng ngày hiện tại
                $datePart = $hoso->ngayTiepNhan 
                    ? \Carbon\Carbon::parse($hoso->ngayTiepNhan)->format('Ymd')
                    : now()->format('Ymd');
                
                // Tạo mã hồ sơ duy nhất
                do {
                    $rand = random_int(1000, 9999);
                    $maHSXL = 'HSXL_' . $IDCD . '_' . $datePart . '_' . $rand;
                } while (self::where('maHSXL', $maHSXL)->exists());

                $hoso->maHSXL = $maHSXL;
            }
        });
    }

    public function congdan()
    {
        return $this->belongsTo(CongDan::class, 'IDCD', 'IDCD');
    }

    public function tthc()
    {
        return $this->belongsTo(TTHC::class, 'maTTHC', 'maTTHC');
    }

    public function trangThai()
    {
        return $this->belongsTo(TrangThaiHoSo::class, 'maTrangThai', 'maTrangThai');
    }

    public function mailHistory()
    {
        return $this->hasMany(HoSoXuLyMailHistory::class, 'maHSXL', 'maHSXL');
    }

}
