<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thanhphanhoso', function (Blueprint $table) {
            $table->increments('maThanhPhan');
            $table->integer('maTruongHop')->unsigned();
            $table->integer('tenThanhPhan');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('maTruongHop')
                ->references('maTruongHop')
                ->on('truonghopthuchien');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanhphanhoso');
    }
};
