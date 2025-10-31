<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('truonghopthuchien', function (Blueprint $table) {
            $table->increments('maTruongHop');
            $table->integer('maTTHC')->unsigned();
            $table->string('tenTruongHop');

            $table->foreign('maTTHC')->references('maTTHC')->on('TTHC');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truonghopthuchien');
    }
};
