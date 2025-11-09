<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrangThaiHoSoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('trangthaihoso')->insert([
            [
                'maTrangThai'=>1,
                'tenTrangThai'=>'Được tiếp nhận'
            ],
            [
                'maTrangThai'=>2,
                'tenTrangThai'=>'Không được tiếp nhân'
            ],
            [
                'maTrangThai'=>3,
                'tenTrangThai'=>'Đang xử lý'
            ],
            [
                'maTrangThai'=>4,
                'tenTrangThai'=>'Yêu cầu bổ sung giấy tờ'
            ],
            [
                'maTrangThai'=>5,
                'tenTrangThai'=>'Yêu cầu thực hiện nghĩa vụ tài chính'
            ],
            [
                'maTrangThai'=>6,
                'tenTrangThai'=>'Công dân yêu cầu rút hồ sơ'
            ],
            [
                'maTrangThai'=>7,
                'tenTrangThai'=>'Dửng xử lý'
            ],
            [
                'maTrangThai'=>8,
                'tenTrangThai'=>'Đã xử lý xong'
            ],
            [
                'maTrangThai'=>9,
                'tenTrangThai'=>'Đã trả kết quả'
            ]
        ]);
    }
}
