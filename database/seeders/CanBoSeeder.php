<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CanBoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('canbo')->insert([
            [
                'IDCB' => 1,
                'IDnguoiDung' => 1,     // liên kết đến bảng nguoi (vaiTro = 'Cán bộ')
                'maQuayLamViec' => '1'
            ],
            // [
            //     'IDCB' => 2,
            //     'IDnguoiDung' => 4,     // ví dụ cán bộ thứ 2
            //     'maQuayLamViec' => '2'
            // ],
            // [
            //     'IDCB' => 3,
            //     'IDnguoiDung' => 6,     // nếu bạn có thêm người thứ 6 là cán bộ
            //     'maQuayLamViec' => '3'
            // ],
        ]);
    }
}
