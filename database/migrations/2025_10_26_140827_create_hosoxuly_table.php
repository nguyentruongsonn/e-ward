<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosoxuly', function (Blueprint $table) {
            $table->increments('maHSXL');
            $table->integer('maTTHC')->unsigned();
            $table->integer('IDCD')->unsigned();
            $table->string('tenChuHoSo', 255);
            $table->string('doiTuongThucHien', 255)->nullable();
            $table->string('email', 255);
            $table->string('soDienThoai', 10);
            $table->date('ngayTiepNhan')->nullable();
            $table->date('ngayHenTra')->nullable();
            $table->integer('maTrangThai')->unsigned();
            $table->date('ngayTra')->nullable();
            $table->integer('hanBoSung')->nullable();
            $table->text('thongTinTra')->nullable();
            $table->float('lePhi');
            $table->enum('hinhThuc', ['Nhận trực tiếp', 'Nhận trực tuyến']);
            $table->date('ngayKetThucXuLy')->nullable();
            $table->string('donViXuLy', 255);
            $table->text('ghiChu')->nullable();
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('maTTHC')
                ->references('maTTHC')
                ->on('tthc');

            $table->foreign('maTrangThai')
                ->references('maTrangThai')
                ->on('trangthaihoso');


            $table->foreign('IDCD')
                ->references('IDCD')
                ->on('congdan');


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosoxuly');
    }
};
