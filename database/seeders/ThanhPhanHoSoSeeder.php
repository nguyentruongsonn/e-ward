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

                'maTTHC' => 3,
                'tenThanhPhan' => 'Bao gồm'
            ],

            [

                'maTTHC' => 3,
                'tenThanhPhan' => '* Giấy tờ xuất trình'
            ],
//=================Thủ tục đăng ký kết hôn
            [

                'maTTHC' => 2,
                'tenThanhPhan' => '* Giấy tờ phải xuất trình'
            ],

            [

                'maTTHC' => 2,
                'tenThanhPhan' => '* Lưu ý'
            ],
            [

                'maTTHC' => 2,
                'tenThanhPhan' => 'Bao gồm'
            ],

    //=================Cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh
            [

                'maTTHC'=>1,
                'tenThanhPhan'=>'*Giấy tờ phải nộp'
            ],
            [

                'maTTHC'=>1,
                'tenThanhPhan'=>'* Giấy tờ phải xuất trình'
            ],
            [

                'maTTHC'=>1,
                'tenThanhPhan'=>'* Lưu ý'
            ],
            [
                'maTTHC'=>1,
                'tenThanhPhan'=>'Bao gồm'
            ],
    // ============Thủ tục chứng thực chữ ký trong các giấy tờ, văn bản (áp dụng cho cả trường hợp chứng thực điểm chỉ và trường hợp người yêu cầu chứng thực không thể ký, không thể điểm chỉ được)
            [

                'maTTHC'=>4,
                'tenThanhPhan'=>'Người yêu cầu chứng thực phải xuất trình giấy tờ sau'
            ],
    //==============Chứng thực bản sao từ bản chính giấy tờ, văn bản do cơ quan, tổ chức có thẩm quyền của Việt Nam; cơ quan, tổ chức có thẩm quyền của nước ngoài; cơ quan, tổ chức có thẩm quyền của Việt Nam liên kết với cơ quan, tổ chức có thẩm quyền của nước ngoài cấp hoặc chứng nhận
            [

                'maTTHC'=>5,
                'tenThanhPhan'=>'Bao gồm'
            ],
    //===============Xác nhận thông tin về cư trú
            [

                'maTTHC'=>6,
                'tenThanhPhan'=>'Bao gồm'
            ],

    //==============Xóa đăng ký tạm trú
            [

                'maTTHC'=>7,
                'tenThanhPhan'=>'Bao gồm'
            ],

    //============Cấp Phiếu lý lịch tư pháp cho công dân Việt Nam, người nước ngoài đang cư trú tại Việt Nam
            [

                'maTTHC'=>8,
                'tenThanhPhan'=>'Bao gồm'
            ],

    //============= Liên thông các thủ tục hành chính về đăng ký khai sinh, cấp Thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi
    //maThanhPhan = 15
            [
                'maTTHC' => 9,
                'tenThanhPhan' =>'Bao gồm'
            ]
        ]);
    }
}
