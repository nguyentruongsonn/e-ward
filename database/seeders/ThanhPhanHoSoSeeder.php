<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhPhanHoSoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thanhphanhoso')->insert([
            [
                'maThanhPhan' => 1,
                'maTTHC' => 3,
                'tenThanhPhan' => 'Bao gồm'
            ],

            [
                'maThanhPhan' => 2,
                'maTTHC' => 3,
                'tenThanhPhan' => '* Giấy tờ xuất trình'
            ],


        ]);
    }
}
