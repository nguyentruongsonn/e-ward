<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GiayToSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('giayto')->insert([
            [
                'maGiayTo' => '1',
                'tenGiayTo' => 'Tờ khai đăng ký kết hôn theo mẫu, có đủ thông tin của hai bên nam, nữ. Hai bên nam, nữ có thể khai chung vào một Tờ khai đăng ký kết hôn (nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tiếp);',
                'loaiGiayTo' =>'Tờ khai'
            ],
            [
                'maGiayTo' => '2',
                'tenGiayTo' => '- Mẫu hộ tịch điện tử tương tác đăng ký kết hôn (do người yêu cầu cung cấp thông tin theo hướng dẫn trên Cổng dịch vụ công, nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tuyến)',
                'loaiGiayTo' =>'Mẫu đơn'
            ],
            [
                'maGiayTo' => '3',
                'tenGiayTo' => '- Người có yêu cầu đăng ký kết hôn thực hiện việc nộp/xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến) các giấy tờ sau:',
                'loaiGiayTo' =>'Mẫu đơn'
            ],
                        [
                'maGiayTo' => '4',
                'tenGiayTo' => '- Hộ chiếu/Chứng minh nhân dân/Thẻ căn cước công dân/Thẻ căn cước/Căn cước điện tử/Giấy chứng nhận căn cước hoặc các giấy tờ khác có dán ảnh và thông tin cá nhân do cơ quan có thẩm quyền cấp, còn giá trị sử dụng để chứng minh về nhân thân của người có yêu cầu đăng ký kết hôn. Trường hợp các thông tin cá nhân trong các giấy tờ này đã có trong CSDLQGVDC, CSDLHTĐT, được hệ thống điền tự động thì không phải tải lên (theo hình thức trực tuyến);',
                'loaiGiayTo' =>'Tờ khai'
            ],
            [
                'maGiayTo' => '5',
                'tenGiayTo' => '- Giấy tờ có giá trị chứng minh thông tin về cư trú trong trường hợp cơ quan đăng ký hộ tịch không thể khai thác được thông tin về nơi cư trú của công dân theo các phương thức quy định tại khoản 2 Điều 14 Nghị định số 104/2022/NĐ-CP ngày 21/12/2022 của Chính phủ. Trường hợp các thông tin về giấy tờ chứng minh nơi cư trú đã được khai thác từ Cơ sở dữ liệu quốc gia về dân cư bằng các phương thức này thì người có yêu cầu không phải xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến).',
                'loaiGiayTo' =>'Tờ khai'
            ],

        ]);
    }
}
