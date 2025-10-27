<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TTHC extends Model
{
    use HasFactory;

    protected $table = 'tthc';           // tên bảng
    protected $primaryKey = 'maTTHC';    // khóa chính
    protected $fillable = [
        'tenTTHC',
        'maLinhVuc',
        'maQuayLamViec',
        'doiTuongThucHien',
        'trinhTuThucHien',
        'thoiHanGiaiQuyet',
        'phi',
        'lePhi',
        'yeuCauDieuKien',
        'canCuPhapLy',
        'ketQuaThucHien'
    ];
}
