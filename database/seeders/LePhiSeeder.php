<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class LePhiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lephi')->insert([
            [
                'maLePhi'=>1,
                'loaiLePhi'=>'Lệ phí',
                'maTTHC'=>2,
                'soTien'=>0,
                'batBuoc'=>'Không',
                'moTa'=>'Thu phí bằng 0 đồng trường hợp nộp hồ sơ trực tuyến (Theo Nghị quyết: 08/2025/NQ-HĐND ngày 23/06/2025)'
            ],

            [
                'maLePhi'=>2,
                'loaiLePhi'=>'Phí số lượng bản',
                'maTTHC'=>2,
                'soTien'=>2000,
                'batBuoc'=>'Không',
                'moTa'=>'8000/ bản sao'
            ],
        ]);
    }
}
