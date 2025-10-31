<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nguoi extends Model
{
    use HasFactory;

    protected $table = 'nguoi';
    protected $primaryKey = 'IDnguoiDung';
    public $timestamps = false; // Nếu bảng không có created_at / updated_at

    protected $fillable = [
        'maCCCD',
        'hoTen',
        'gioiTinh',
        'ngaySinh',
        'queQuan',
        'noiThuongTru',
        'noiTamTru',
        'email',
        'soDienThoai',
        'vaiTro',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'IDnguoiDung', 'IDnguoiDung');
    }
}
