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

    public $timestamps = false;

    public function linhVuc()
    {
        return $this->belongsTo(\App\Models\LinhVuc::class, 'maLinhVuc', 'maLinhVuc');
    }

    public function cachThucHiens()
    {
        return $this->hasMany(\App\Models\CachThucHien::class, 'maTTHC', 'maTTHC');
    }

    public function thanhPhanHoSos()
    {
        return $this->hasMany(\App\Models\ThanhPhanHoSo::class, 'maTTHC', 'maTTHC');
    }

    public function doiTuongs()
    {
        return $this->belongsToMany(\App\Models\DoiTuongThucHien::class, 'thutucdoituong', 'maTTHC', 'maDoiTuong');
    }
}
