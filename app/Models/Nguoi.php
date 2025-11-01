<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Nguoi extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'password',
        'soDienThoai',
        'vaiTro',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'IDnguoiDung', 'IDnguoiDung');
    }
}
