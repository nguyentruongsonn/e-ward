<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HoSoXuLy extends Model
{
    use HasFactory;

    protected $table = 'hosoxuly';
    protected $primaryKey = 'maHSXL';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'maHSXL',
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
    ];

    protected $casts = [
        'maHSXL' => 'string',
        'dulieu' => 'array',
        'ngayTiepNhan' => 'date',
        'ngayHenTra' => 'date',
        'ngayTra' => 'date',
        'ngayKetThucXuLy' => 'date',
        'lePhi' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hoSoXuLy) {
            // Tự động tạo maHSXL nếu chưa có
            if (empty($hoSoXuLy->maHSXL)) {
                $IDCD = $hoSoXuLy->IDCD ?? 0;
                do {
                    $rand = random_int(1000, 9999);
                    $maHSXL = 'HSXL_' . $IDCD . '_' . now('Asia/Ho_Chi_Minh')->format('Ymd') . '_' . $rand;
                } while (self::where('maHSXL', $maHSXL)->exists());
                $hoSoXuLy->maHSXL = $maHSXL;
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

    // Lưu ý: Relationship lichHenGanNhat được set thủ công trong Controller bằng setRelation
    // Không định nghĩa relationship ở đây để tránh lỗi khi Laravel cố gắng resolve relationship
    // View vẫn có thể sử dụng $hoSo->lichHenGanNhat vì Controller đã set bằng setRelation
}
