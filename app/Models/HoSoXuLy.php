<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSoXuLy extends Model
{
    use HasFactory;

    protected $table = 'hosoxuly';
    protected $primaryKey = 'maHSXL';
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
    ];

    protected $casts = [
        'dulieu' => 'array',
        'ngayTiepNhan' => 'date',
        'ngayHenTra' => 'date',
        'ngayTra' => 'date',
        'ngayKetThucXuLy' => 'date',
        'lePhi' => 'float',
    ];

    public function congdan()
    {
        return $this->belongsTo(CongDan::class, 'IDCD', 'IDCD');
    }

    public function tthc()
    {
        return $this->belongsTo(TTHC::class, 'maTTHC', 'maTTHC');
    }

}
