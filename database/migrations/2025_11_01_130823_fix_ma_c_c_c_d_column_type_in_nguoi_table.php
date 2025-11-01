<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sửa kiểu cột maCCCD từ INT sang VARCHAR để lưu được mã CCCD 12 chữ số
        DB::statement('ALTER TABLE `nguoi` MODIFY COLUMN `maCCCD` VARCHAR(20) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không rollback để tránh mất dữ liệu
        // DB::statement('ALTER TABLE `nguoi` MODIFY COLUMN `maCCCD` INT NULL');
    }
};
