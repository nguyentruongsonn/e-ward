<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHinhForm extends Model
{
    use HasFactory;

    protected $table = 'formtructuyen';
    protected $primaryKey = 'maForm';       // hoặc 'maCauHinh' nếu bạn dùng khóa khác       // tắt timestamps nếu bảng không có created_at, updated_at
    public $incrementing = true;
    protected $fillable = [
        'maForm',
        'maTTHC',
        'cauHinhForm',
    ];
}
