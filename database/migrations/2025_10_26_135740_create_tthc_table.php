<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tthc', function (Blueprint $table) {
            $table->increments('maTTHC');
            $table->string('tenTTHC', 500);
            $table->integer('maLinhVuc')->unsigned();
            $table->integer('maQuayLamViec')->unsigned();
            $table->string('doiTuongThucHien', 500);
            $table->text('trinhTuThucHien');
            $table->string('thoiHanGiaiQuyet', 500);
            $table->float('phi');
            $table->float('lePhi');
            $table->string('yeuCauDieuKien', 500);
            $table->string('canCuPhapLy', 500);
            $table->string('ketQuaThucHien', 500);
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('maLinhVuc')
                ->references('maLinhVuc')
                ->on('linhvuc');

            $table->foreign('maQuayLamViec')
                ->references('maQuayLamViec')
                ->on('quaylamviec');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tthc');
    }
};
