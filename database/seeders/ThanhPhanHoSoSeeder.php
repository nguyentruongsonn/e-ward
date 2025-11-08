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
//=================Thủ tục đăng ký kết hôn
            [
                'maThanhPhan' => 3,
                'maTTHC' => 2,
                'tenThanhPhan' => '* Giấy tờ phải xuất trình'
            ],

            [
                'maThanhPhan' => 4,
                'maTTHC' => 2,
                'tenThanhPhan' => '* Lưu ý'
            ],
            [
                'maThanhPhan' => 5,
                'maTTHC' => 2,
                'tenThanhPhan' => 'Bao gồm'
            ],

    //=================Cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh
            [
                'maThanhPhan'=>6,
                'maTTHC'=>1,
                'tenThanhPhan'=>'*Giấy tờ phải nộp'
            ],
            [
                'maThanhPhan'=>7,
                'maTTHC'=>1,
                'tenThanhPhan'=>'* Giấy tờ phải xuất trình'
            ],
            [
                'maThanhPhan'=>8,
                'maTTHC'=>1,
                'tenThanhPhan'=>'* Lưu ý'
            ],
            [
                'maThanhPhan'=>9,
                'maTTHC'=>1,
                'tenThanhPhan'=>'Bao gồm'
            ],
    // ============Thủ tục chứng thực chữ ký trong các giấy tờ, văn bản (áp dụng cho cả trường hợp chứng thực điểm chỉ và trường hợp người yêu cầu chứng thực không thể ký, không thể điểm chỉ được)
            [
                'maThanhPhan'=>10,
                'maTTHC'=>4,
                'tenThanhPhan'=>'Người yêu cầu chứng thực phải xuất trình giấy tờ sau'
            ],
    //==============Chứng thực bản sao từ bản chính giấy tờ, văn bản do cơ quan, tổ chức có thẩm quyền của Việt Nam; cơ quan, tổ chức có thẩm quyền của nước ngoài; cơ quan, tổ chức có thẩm quyền của Việt Nam liên kết với cơ quan, tổ chức có thẩm quyền của nước ngoài cấp hoặc chứng nhận
            [
                'maThanhPhan'=>11,
                'maTTHC'=>5,
                'tenThanhPhan'=>'Bao gồm'
            ],
    //===============Xác nhận thông tin về cư trú
            [
                'maThanhPhan'=>12,
                'maTTHC'=>6,
                'tenThanhPhan'=>'Bao gồm'
            ],

    //==============Xóa đăng ký tạm trú
            [
                'maThanhPhan'=>13,
                'maTTHC'=>7,
                'tenThanhPhan'=>'Bao gồm'
            ],

    //============Cấp Phiếu lý lịch tư pháp cho công dân Việt Nam, người nước ngoài đang cư trú tại Việt Nam
            [
                'maThanhPhan'=>14,
                'maTTHC'=>8,
                'tenThanhPhan'=>'Bao gồm'
            ],
        ]);
    }
}
