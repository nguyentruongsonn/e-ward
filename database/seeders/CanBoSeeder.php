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
                'IDnguoiDung' => 2,     // liên kết đến bảng nguoi (chucVu = 'Cán bộ')
                'maQuayLamViec' => '1',
                'chucVu' => 'Cán bộ một cửa'
            ],
            [
                'IDCB' => 2,
                'IDnguoiDung' => 3,     // ví dụ cán bộ thứ 2
                'maQuayLamViec' => '2',
                'chucVu' => 'Lãnh đạo'
            ],
            // [
            //     'IDCB' => 3,
            //     'IDnguoiDung' => 6,     // nếu bạn có thêm người thứ 6 là cán bộ
            //     'maQuayLamViec' => '3'
            // ],
        ]);
    }
}
