<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhPhanGiayToSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thanhphangiayto')->insert([
            [
                'maThanhPhan'=>1,
                'maGiayTo'=>1,
                'soLuongBanChinh'=>1,
                'soLuongBanSao'=>0
            ],
                        [
                'maThanhPhan'=>1,
                'maGiayTo'=>2,
                'soLuongBanChinh'=>1,
                'soLuongBanSao'=>0
            ],
                        [
                'maThanhPhan'=>2,
                'maGiayTo'=>3,
                'soLuongBanChinh'=>0,
                'soLuongBanSao'=>0
            ],
                        [
                'maThanhPhan'=>2,
                'maGiayTo'=>4,
                'soLuongBanChinh'=>0,
                'soLuongBanSao'=>0
            ],
                        [
                'maThanhPhan'=>2,
                'maGiayTo'=>5,
                'soLuongBanChinh'=>0,
                'soLuongBanSao'=>0
            ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],            [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
            //             [
            //     'maThanhPhan'=>,
            //     'maGiayTo'=>,
            //     'soLuongBanChinh'=>,
            //     'soLuongBanSao'=>
            // ],
        ]);
    }
}
