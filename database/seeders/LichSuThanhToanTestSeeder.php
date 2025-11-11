<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LichSuThanhToanTestSeeder extends Seeder
{
	public function run(): void
	{
		// Lấy IDCD bất kỳ; nếu chưa có thì tạo tạm
		$IDCD = DB::table('congdan')->value('IDCD');
		if (!$IDCD) {
			$IDCD = DB::table('congdan')->insertGetId([
				'IDnguoiDung' => null,
			]);
		}

		$maHSXL = DB::table('hosoxuly')->where('IDCD', $IDCD)->value('maHSXL');

		$maGD = 'TEST-' . Str::uuid();
		$soGD = 'VNPAY-' . random_int(100000, 999999);

		// Tránh trùng unique maGD
		if (!DB::table('lichsuthanhtoan')->where('maGD', $maGD)->exists()) {
			DB::table('lichsuthanhtoan')->insert([
				'maGD' => $maGD,
				'soGD' => $soGD,
				'loaiGD' => 'VNPAY',
				'ngayGD' => Carbon::now(),
				'soTien' => 123000,
				'trangThai' => 'THANH_CONG',
				'IDCD' => $IDCD,
				'maHSXL' => $maHSXL,
				'moTa' => 'Seeded test payment history record',
			]);
		}
	}
}


