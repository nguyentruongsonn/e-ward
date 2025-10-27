<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguoi', function (Blueprint $table) {
            $table->increments('IDNguoiDung');
            $table->integer('maCCCD');
            $table->integer('hoTen'); // giữ nguyên kiểu như SQL (int 255)
            $table->enum('gioiTinh', ['Nam', 'Nữ'])->nullable();
            $table->date('ngaySinh')->nullable();
            $table->string('queQuan', 500)->nullable();
            $table->string('noiThuongTru', 500)->nullable();
            $table->string('noiTamTru', 500)->nullable();
            $table->string('email', 255);
            $table->string('soDienThoai', 10);
            $table->enum('vaiTro', ['Công dân/ Tổ chức', 'Cán bộ', 'Lãnh đạo', 'Quản trị viên']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoi');
    }
};
