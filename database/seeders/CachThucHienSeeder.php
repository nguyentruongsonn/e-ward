<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CachThucHienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cachthuchien')->insert([
            [
                'maCTH'=> 1,
                'maTTHC'=>'1',
                'kenh'=> 'Trực tiếp',
                'thoiHanGiaiQuyet'=>'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.',
                'moTaPhiLePhi'=>'Lệ phí : 8.000 đồng/bản sao Trích lục/sự kiện hộ tịch đã đăng ký Miễn lệ phí cho người thuộc gia đình có công với cách mạng; người thuộc hộ nghèo; người khuyết tật.',
                'moTa'=>'Người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia ',
            ],
            [
                'maCTH'=> 2,
                'maTTHC'=>'1',
                'kenh'=> 'Trực tuyến',
                'thoiHanGiaiQuyet'=>'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.',
                'moTaPhiLePhi'=>'Lệ phí : 8.000 đồng/bản sao Trích lục/sự kiện hộ tịch đã đăng ký Miễn lệ phí cho người thuộc gia đình có công với cách mạng; người thuộc hộ nghèo; người khuyết tật.',
                'moTa'=>'- Thủ tục này đủ điều kiện thực hiện dịch vụ công trực tuyến toàn trình. - Người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia ',
            ],
            [
                'maCTH'=> 3,
                'maTTHC'=>'1',
                'kenh'=> 'Dịch vụ bưu chính',
                'thoiHanGiaiQuyet'=>'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.',
                'moTaPhiLePhi'=>'Lệ phí : 8.000 đồng/bản sao Trích lục/sự kiện hộ tịch đã đăng ký Miễn lệ phí cho người thuộc gia đình có công với cách mạng; người thuộc hộ nghèo; người khuyết tật.',
                'moTa'=>'Người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia  ',
            ],
            [
                'maCTH'=> 4,
                'maTTHC'=>'2',
                'kenh'=> 'Trực tiếp',
                'thoiHanGiaiQuyet'=>'03 Ngày làm việc',
                'moTaPhiLePhi'=>'Phí : 10.000 Đồng - Đăng ký tạm trú theo danh sách: + Trường hợp công dân nộp hồ sơ trực tiếp thu 10.000 đồng/người/lần đăng ký;
                    Phí : 15.000 Đồng Đăng ký tạm trú (cá nhân, hộ gia đình): + Trường hợp công dân nộp hồ sơ trực tiếp thu 15.000 đồng/lần đăng ký;
                    Phí : Miễn phí Đồng - Trường hợp công dân thuộc diện được miễn phí theo quy định tại Điều 4 Thông tư số 75/2022/TT-BTC ngày 22/12/2022 quy định mức thu, chế độ thu, nộp và quản lý lệ phí đăng ký cư trú thì công dân phải xuất trình giấy tờ chứng minh thuộc diện được miễn trừ trường hợp thông tin đã có trong Cơ sở dữ liệu quốc gia về dân cư hoặc Cơ sở dữ liệu quốc gia, Cơ sở dữ liệu chuyên ngành mà đã được kết nối với Cơ sở dữ liệu quốc gia về dân cư.',
                'moTa'=>'- Nộp hồ sơ trực tiếp tại Công an cấp xã. Thời gian tiếp nhận hồ sơ: Giờ hành chính các ngày làm việc từ thứ 2 đến thứ 6 và sáng thứ 7 hàng tuần (trừ các ngày nghỉ lễ, tết theo quy định của pháp luật).',
            ],
            [
                'maCTH'=> 5,
                'maTTHC'=>'2',
                'kenh'=> 'Trực tuyến',
                'thoiHanGiaiQuyet'=>'03 Ngày làm việc',
                'moTaPhiLePhi'=>'Phí : 5.000 Đồng - Đăng ký tạm trú theo danh sách: + Trường hợp công dân nộp hồ sơ qua cổng dịch vụ công trực tuyến thu 5.000 đồng/người/lần đăng ký.
                    Phí : 7.000 Đồng - Đăng ký tạm trú (cá nhân, hộ gia đình): + Trường hợp công dân nộp hồ sơ qua cổng dịch vụ công trực tuyến thu 7.000 đồng/lần đăng ký.
                    Phí : Miễn phí Đồng - Trường hợp công dân thuộc diện được miễn phí theo quy định tại Điều 4 Thông tư số 75/2022/TT-BTC ngày 22/12/2022 quy định mức thu, chế độ thu, nộp và quản lý lệ phí đăng ký cư trú thì công dân phải xuất trình giấy tờ chứng minh thuộc diện được miễn trừ trường hợp thông tin đã có trong Cơ sở dữ liệu quốc gia về dân cư hoặc Cơ sở dữ liệu quốc gia, Cơ sở dữ liệu chuyên ngành mà đã được kết nối với Cơ sở dữ liệu quốc gia về dân cư.',
                'moTa'=>'- Nộp hồ sơ trực tuyến qua các cổng cung cấp dịch vụ công trực tuyến như: Trực tuyến qua Cổng dịch vụ công quốc gia, Cổng dịch vụ công Bộ Công an, ứng dụng VNeID hoặc dịch vụ công trực tuyến khác theo quy định của pháp luật. Thời gian tiếp nhận hồ sơ: Giờ hành chính các ngày làm việc từ thứ 2 đến thứ 6 và sáng thứ 7 hàng tuần (trừ các ngày nghỉ lễ, tết theo quy định của pháp luật).',
            ],
            [
                'maCTH'=> 6,
                'maTTHC'=>'3',
                'kenh'=> 'Trực tiếp',
                'thoiHanGiaiQuyet'=>'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.Trường hợp cần xác minh điều kiện kết hôn của hai bên nam, nữ thì thời hạn giải quyết không quá 05 ngày làm việc.',
                'moTaPhiLePhi'=>'Lệ phí : Miễn lệ phí. Phí cấp bản sao Trích lục kết hôn (nếu có yêu cầu) thực hiện theo quy định tại Thông tư số 281/2016/TT-BTC ngày 14/11/2016 của Bộ Tài chính.',
                'moTa'=>'Người có yêu cầu đăng ký kết hôn thực hiện nộp hồ sơ trực tiếp tại Bộ phận một cửa của UBND cấp xã hoặc nộp hồ sơ trực tuyến trên Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn) hoặc Cổng dịch vụ công cấp tỉnh (https://dichvucong.---.gov.vn) (bên nam hoặc bên nữ có thể nộp hồ sơ mà không cần có văn bản ủy quyền của bên còn lại).',
            ],

            [
                'maCTH'=> 7,
                'maTTHC'=>'3',
                'kenh'=> 'Trực tuyến',
                'thoiHanGiaiQuyet'=>'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo. Trường hợp cần xác minh điều kiện kết hôn của hai bên nam, nữ thì thời hạn giải quyết không quá 05 ngày làm việc.',
                'moTaPhiLePhi'=>'Lệ phí : Đồng Miễn lệ phí. Phí cấp bản sao Trích lục kết hôn (nếu có yêu cầu) thực hiện theo quy định tại Thông tư số 281/2016/TT-BTC ngày 14/11/2016 của Bộ Tài chính.',
                'moTa'=>'Người có yêu cầu đăng ký kết hôn thực hiện nộp hồ sơ trực tiếp tại Bộ phận một cửa của UBND cấp xã hoặc nộp hồ sơ trực tuyến trên Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn) hoặc Cổng dịch vụ công cấp tỉnh (https://dichvucong.---.gov.vn) (bên nam hoặc bên nữ có thể nộp hồ sơ mà không cần có văn bản ủy quyền của bên còn lại).',
            ],

            [
                'maCTH'=> 8,
                'maTTHC'=>'3',
                'kenh'=> 'Dịch vụ bưu chính',
                'thoiHanGiaiQuyet'=>'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo. Trường hợp cần xác minh điều kiện kết hôn của hai bên nam, nữ thì thời hạn giải quyết không quá 05 ngày làm việc.',
                'moTaPhiLePhi'=>'Lệ phí : Đồng Miễn lệ phí. Phí cấp bản sao Trích lục kết hôn (nếu có yêu cầu) thực hiện theo quy định tại Thông tư số 281/2016/TT-BTC ngày 14/11/2016 của Bộ Tài chính.',
                'moTa'=>'Người có yêu cầu đăng ký kết hôn thực hiện nộp hồ sơ trực tiếp tại Bộ phận một cửa của UBND cấp xã hoặc nộp hồ sơ trực tuyến trên Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn) hoặc Cổng dịch vụ công cấp tỉnh (https://dichvucong.---.gov.vn) (bên nam hoặc bên nữ có thể nộp hồ sơ mà không cần có văn bản ủy quyền của bên còn lại).',
            ],
        ]);
    }
}
