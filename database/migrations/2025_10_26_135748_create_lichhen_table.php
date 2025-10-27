<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lichhen', function (Blueprint $table) {
            $table->increments('maLichHen');
            $table->integer('IDCD')->unsigned();
            $table->integer('maTTHC')->unsigned();
            $table->dateTime('thoiGianHen');
            $table->enum('trangThai', ['Chờ đến', 'Đã hủy', 'Đang xử lý', 'Hoàn thành', 'Không đến']);
            $table->integer('maQuayLamViec')->unsigned();
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('IDCD')
                ->references('IDCD')
                ->on('congdan')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('maTTHC')
                ->references('maTTHC')
                ->on('tthc');

            $table->foreign('maQuayLamViec')
                ->references('maQuayLamViec')
                ->on('quaylamviec');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lichhen');
    }
};
