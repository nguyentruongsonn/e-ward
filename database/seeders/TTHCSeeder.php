<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TTHCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tthc')->insert([
            [
            'maTTHC'=>1,
            'tenTTHC'=>'Cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh',
            'maLinhVuc'=>'1',
            'maQuayLamViec'=>'1',
            'trinhTuThucHien'=>'
            - Nếu lựa chọn hình thức nộp hồ sơ trực tiếp, người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh nộp hồ sơ đề nghị cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh tại Trung tâm Phục vụ hành chính công có thẩm quyền; nộp phí theo quy định pháp luật.
            Trường hợp cơ quan, tổ chức có thẩm quyền đề nghị cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh của cá nhân thì gửi văn bản yêu cầu nêu rõ lý do cho Cơ quan quản lý Cơ sở dữ liệu hộ tịch điện tử.
            - Nếu lựa chọn hình thức nộp hồ sơ trực tuyến, người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh truy cập Cổng dịch vụ công quốc gia, xác thực người dùng theo hướng dẫn, đăng nhập vào hệ thống, lựa chọn Cơ quan quản lý Cơ sở dữ liệu hộ tịch điện tử có thẩm quyền.
            Người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tuyến cung cấp thông tin theo mẫu điện tử tương tác yêu cầu bản sao Trích lục hộ tịch, cấp bản sao Giấy khai sinh (cung cấp trên Cổng dịch vụ công), đính kèm bản chụp hoặc bản sao điện tử các giấy tờ, tài liệu liên quan đến nội dung đề nghị cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh; nộp phí thông qua chức năng thanh toán trực tuyến hoặc bằng cách thức khác theo quy định pháp luật, hoàn tất việc nộp hồ sơ.
            - Cán bộ tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công có trách nhiệm kiểm tra tính chính xác, đầy đủ, thống nhất, hợp lệ của hồ sơ.
            (i) Trường hợp hồ sơ đầy đủ, hợp lệ thì tiếp nhận hồ sơ; nếu tiếp nhận hồ sơ sau 15 giờ thì có Phiếu hẹn, trả kết quả cho người yêu cầu trong ngày làm việc tiếp theo (nếu người yêu cầu nộp hồ sơ trực tiếp) hoặc gửi ngay Phiếu hẹn, trả kết quả qua thư điện tử hoặc gửi tin nhắn hẹn trả kết quả qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến); chuyển hồ sơ để công chức tư pháp - hộ tịch xử lý; trường hợp tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công cấp tỉnh, cán bộ tiếp nhận hồ sơ chuyển hồ sơ đến Sở Tư pháp/Ủy ban nhân dân cấp xã có thẩm quyền xử lý.
            (ii) Trường hợp hồ sơ chưa đầy đủ, hợp lệ thì cán bộ tiếp nhận hồ sơ thông báo cho người yêu cầu bổ sung, hoàn thiện hồ sơ, nêu rõ loại giấy tờ, nội dung cần bổ sung để người yêu cầu bổ sung, hoàn thiện. Sau khi hồ sơ được bổ sung, thực hiện lại bước (i);
            (iii) Trường hợp người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh không bổ sung, hoàn thiện được hồ sơ thì cán bộ tiếp nhận hồ sơ báo cáo Lãnh đạo Trung tâm Phục vụ hành chính công có thông báo từ chối giải quyết yêu cầu cấp bản sao Trích lục hộ tịch, bản sao giấy khai sinh.
            - Công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch thẩm tra hồ sơ (thẩm tra tính thống nhất, hợp lệ của các thông tin trong hồ sơ, giấy tờ, tài liệu do người yêu cầu nộp, xuất trình hoặc đính kèm).
            + Trường hợp hồ sơ cần bổ sung, hoàn thiện hoặc không đủ điều kiện giải quyết, phải từ chối thì công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch gửi thông báo về tình trạng hồ sơ tới Trung tâm Phục vụ hành chính công để thông báo cho người nộp hồ sơ – thực hiện lại bước (ii) hoặc (iii);
            + Trường hợp cần phải kiểm tra, xác minh làm rõ hoặc do nguyên nhân khác mà không thể trả kết quả đúng thời gian đã hẹn thì công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch lập Phiếu xin lỗi và hẹn lại ngày trả kết quả, trong đó nêu rõ lý do chậm trả kết quả và thời gian hẹn trả kết quả, chuyển Trung tâm Phục vụ hành chính công để trả cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tiếp), hoặc gửi Phiếu xin lỗi và hẹn lại ngày trả kết quả qua thư điện tử hoặc gửi tin nhắn qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến).
            + Nếu thấy hồ sơ đầy đủ, hợp lệ, đúng quy định, trường hợp tiếp nhận hồ sơ cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh theo hình thức trực tiếp, thì công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch in bản sao Trích lục hộ tịch trình Thủ trưởng Cơ quan quản lý Cơ sở dữ liệu hộ tịch điện tử ký, chuyển tới Trung tâm Phục vụ hành chính công để trả kết quả cho người yêu cầu.
            Trường hợp tiếp nhận hồ sơ xin cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh theo hình thức trực tuyến, công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch gửi lại nội dung biểu mẫu Trích lục hộ tịch điện tử tương ứng với thông tin đầy đủ cho người yêu cầu qua thư điện tử hoặc thiết bị số.
            Người yêu cầu có trách nhiệm kiểm tra tính chính xác, đầy đủ của các thông tin trên biểu mẫu Trích lục hộ tịch điện tử và xác nhận (tối đa một ngày).
            Nếu người có yêu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh đã thống nhất, đầy đủ hoặc không có phản hồi sau thời hạn một ngày thì công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch in bản sao Trích lục hộ tịch, trình Thủ trưởng Cơ quan quản lý Cơ sở dữ liệu hộ tịch điện tử ký, chuyển tới Trung tâm Phục vụ hành chính công để trả kết quả cho người yêu cầu.

            ',

            'coQuanThucHien'=>'Cơ quan quản lý cơ sở dữ liệu hộ tịch điện tử',
            'trangThai'=>'Công khai',
            'yeuCauDieuKien'=>'Không',
            'canCuPhapLy'=>'Luật 60/2014/QH13',
            'ketQuaThucHien'=>'Trích lục ghi vào Sổ hộ tịch các việc hộ tịch khác, Bản sao giấy khai sinh',
            ],

            [
            'maTTHC'=>2,
            'tenTTHC'=>'Đăng ký tạm trú',
            'maLinhVuc'=>'3',
            'maQuayLamViec'=>'2',
            'trinhTuThucHien'=>'
                Bước 1: Cá nhân chuẩn bị hồ sơ theo quy định của pháp luật.
                Bước 2: Cá nhân nộp hồ sơ đến Công an cấp xã.
                Bước 3: Khi tiếp nhận hồ sơ đăng ký tạm trú, cơ quan đăng ký cư trú kiểm tra tính pháp lý và nội dung hồ sơ; thực hiện khai thác chứng minh về chỗ ở hợp pháp do công dân cung cấp trong trong căn cước điện tử, tài khoản định danh điện tử trên hệ thống định danh và xác thực điện tử qua Ứng dụng định danh quốc gia hoặc trong Cơ sở dữ liệu quốc gia về dân cư, Cơ sở dữ liệu về cư trú, Kho quản lý dữ liệu điện tử tổ chức, cá nhân trên Cổng dịch vụ công quốc gia, Hệ thống thông tin giải quyết thủ tục hành chính cấp bộ, cấp tỉnh hoặc cơ sở dữ liệu quốc gia, cơ sở dữ liệu chuyên ngành khác. Trường hợp không khai thác được thông tin thì cơ quan đăng ký cư trú có trách nhiệm kiểm tra, xác minh để giải quyết thủ tục về cư trú; công dân có trách nhiệm cung cấp bản sao, bản chụp, bản điện tử một trong các giấy tờ, tài liệu chứng minh về chỗ ở hợp pháp khi cơ quan đăng ký cư trú có yêu cầu.
                - Trường hợp hồ sơ đã đầy đủ, hợp lệ thì tiếp nhận hồ sơ và cấp Phiếu tiếp nhận hồ sơ và hẹn trả kết quả (mẫu CT04 ban hành kèm theo Thông tư số 66/2023/TT-BCA) cho người đăng ký;
                + Chuyển hồ sơ đề nghị xác nhận nơi thường xuyên đậu, đỗ; sử dụng phương tiện vào mục đích để ở hoặc hồ sơ đề nghị xác nhận tình trạng chỗ ở hợp pháp, diện tích nhà ở tối thiểu để đăng ký thường trú, đăng ký tạm trú đến Ủy ban nhân dân cấp xã để xem xét, giải quyết theo quy định (nếu có).
                - Trường hợp hồ sơ đủ điều kiện nhưng chưa đầy đủ thành phần hồ sơ theo quy định của pháp luật thì hướng dẫn bổ sung, hoàn thiện và cấp Phiếu hướng dẫn bổ sung, hoàn thiện hồ sơ (mẫu CT05 ban hành kèm theo Thông tư số 66/2023/TT-BCA) cho người đăng ký;
                - Trường hợp hồ sơ không đủ điều kiện thì từ chối và cấp Phiếu từ chối tiếp nhận, giải quyết hồ sơ (mẫu CT06 ban hành kèm theo Thông tư số 66/2023/TT-BCA) cho người đăng ký.
                Bước 4: Cá nhân, tổ chức nộp lệ phí đăng ký tạm trú theo quy định.
                Bước 5: Căn cứ theo ngày hẹn trên Phiếu tiếp nhận hồ sơ và hẹn trả kết quả để nhận thông báo kết quả giải quyết thủ tục đăng ký cư trú (nếu có).

            ',

            'coQuanThucHien'=>'Công an Xã',
            'trangThai'=>'Công khai',
            'yeuCauDieuKien'=>'Không',
            'canCuPhapLy'=>'Luật 60/2014/QH13',
            'ketQuaThucHien'=>'Cập nhật thông tin trong Cơ sở dữ liệu quốc gia về dân cư, Thông báo kết quả giải quyết thủ tục về cư trú, hủy bỏ thủ tục về cư trú, Cập nhật thông tin trong Cơ sở dữ liệu về cư trú, Phiếu từ chối tiếp nhận, giải quyết hồ sơ (lĩnh vực cư trú)',
            ],

            [
            'maTTHC'=>3,
            'tenTTHC'=>'Thủ tục đăng ký kết hôn',
            'maLinhVuc'=>'1',
            'maQuayLamViec'=>'3',
            'trinhTuThucHien'=>'
                - Nếu lựa chọn hình thức nộp hồ sơ trực tiếp, người yêu cầu đăng ký kết hôn nộp hồ sơ đăng ký kết hôn tại Trung tâm Phục vụ hành chính công có thẩm quyền; nộp lệ phí nếu thuộc trường hợp phải nộp lệ phí đăng ký kết hôn; nộp phí cấp bản sao Trích lục kết hôn nếu có yêu cầu cấp bản sao Trích lục kết hôn.
                - Nếu lựa chọn hình thức nộp hồ sơ trực tuyến, người có yêu cầu đăng ký kết hôn truy cập Cổng dịch vụ công quốc gia hoặc Cổng dịch vụ công cấp tỉnh, đăng ký tài khoản (nếu chưa có tài khoản), xác thực người dùng theo hướng dẫn, đăng nhập vào hệ thống, xác định đúng Ủy ban nhân dân cấp xã có thẩm quyền.
                Người có yêu cầu đăng ký kết hôn trực tuyến cung cấp thông tin theo biểu mẫu điện tử tương tác đăng ký kết hôn (cung cấp trên Cổng dịch vụ công), đính kèm bản chụp hoặc bản sao điện tử các giấy tờ, tài liệu theo quy định; nộp phí, lệ phí thông qua chức năng thanh toán trực tuyến hoặc bằng cách thức khác theo quy định pháp luật, hoàn tất việc nộp hồ sơ.
                - Cán bộ tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công có trách nhiệm kiểm tra tính chính xác, đầy đủ, thống nhất, hợp lệ của hồ sơ.
                (i) Trường hợp hồ sơ đầy đủ, hợp lệ thì tiếp nhận hồ sơ, có Phiếu hẹn, trả kết quả cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tiếp) hoặc gửi ngay Phiếu hẹn, trả kết quả qua thư điện tử hoặc gửi tin nhắn hẹn trả kết quả qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến), chuyển hồ sơ để công chức tư pháp - hộ tịch xử lý; trường hợp tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công cấp tỉnh, cán bộ tiếp nhận hồ sơ chuyển hồ sơ đến Ủy ban nhân dân cấp xã có thẩm quyền xử lý.
                - Sau khi tiếp nhận hồ sơ theo hình thức nộp trực tiếp, cán bộ tiếp nhận hồ sơ tại Bộ phận một cửa thực hiện số hóa (sao chụp, chuyển thành tài liệu điện tử trên hệ thống thông tin, cơ sở dữ liệu) và ký số vào tài liệu, hồ sơ giải quyết thủ tục hành chính đã được số hóa theo quy định.
                (ii) Trường hợp hồ sơ chưa đầy đủ, hợp lệ thì có thông báo cho người yêu cầu bổ sung, hoàn thiện hồ sơ, nêu rõ loại giấy tờ, nội dung cần bổ sung để người có yêu cầu bổ sung, hoàn thiện. Sau khi hồ sơ được bổ sung, thực hiện lại bước (i);
                (iii) Trường hợp người yêu cầu đăng ký kết hôn không bổ sung, hoàn thiện được hồ sơ thì cán bộ tiếp nhận hồ sơ báo cáo Lãnh đạo Trung tâm Phục vụ hành chính công có thông báo từ chối giải quyết yêu cầu đăng ký kết hôn.
                - Công chức tư pháp - hộ tịch thẩm tra hồ sơ (thẩm tra tính thống nhất, hợp lệ của các thông tin trong hồ sơ, giấy tờ, tài liệu đính kèm).
                + Đối với yêu cầu đăng ký kết hôn, cơ quan đăng ký hộ tịch tra cứu thông tin về tình trạng hôn nhân của người yêu cầu đăng ký kết hôn trên Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh thông qua kết nối với Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư. Kết quả tra cứu được lưu trữ dưới dạng điện tử hoặc bản giấy, phản ánh đầy đủ, chính xác thông tin tại thời điểm tra cứu và đính kèm hồ sơ của người đăng ký. Trường hợp không tra cứu được tình trạng hôn nhân do chưa có thông tin trong Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư, thì cơ quan đăng ký hộ tịch đề nghị Ủy ban nhân dân cấp xã nơi người yêu cầu thường trú/nơi đã đăng ký kết hôn xác minh, cung cấp thông tin. Trong thời hạn 03 ngày làm việc kể từ ngày nhận được yêu cầu xác minh, Ủy ban nhân dân cấp xã nơi nhận được đề nghị xác minh có trách nhiệm kiểm tra, xác minh và gửi kết quả về tình trạng hôn nhân của người đó.
                + Nếu bên kết hôn là công dân Việt Nam đã ly hôn hoặc hủy việc kết hôn tại cơ quan có thẩm quyền nước ngoài nhưng qua tra cứu thông tin trong Cơ sở dữ liệu hộ tịch điện tử; thông qua kết nối giữa Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh với Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư không thể hiện thông tin về việc đã ghi chú ly hôn, hủy việc kết hôn thì cơ quan đăng ký hộ tịch hướng dẫn công dân thực hiện thủ tục ghi vào sổ hộ tịch việc ly hôn/hủy việc kết hôn tại cơ quan nhà nước có thẩm quyền trước khi giải quyết việc đăng ký kết hôn.
                + Trường hợp hồ sơ cần bổ sung, hoàn thiện hoặc không đủ điều kiện giải quyết, phải từ chối thì công chức tư pháp - hộ tịch gửi thông báo về tình trạng hồ sơ tới Trung tâm Phục vụ hành chính công để thông báo cho người nộp hồ sơ – thực hiện lại bước (ii) hoặc (iii);
                + Trường hợp cần phải kiểm tra, xác minh làm rõ hoặc do nguyên nhân khác mà không thể trả kết quả đúng thời gian đã hẹn thì công chức tư pháp - hộ tịch lập Phiếu xin lỗi và hẹn lại ngày trả kết quả, trong đó nêu rõ lý do chậm trả kết quả và thời gian hẹn trả kết quả, chuyển Trung tâm Phục vụ hành chính công để trả cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tiếp), hoặc gửi Phiếu xin lỗi và hẹn lại ngày trả kết quả qua thư điện tử hoặc gửi tin nhắn qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến).
                + Nếu thấy hồ sơ đầy đủ, hợp lệ, các bên có đủ điều kiện kết hôn theo quy định của Luật Hôn nhân và gia đình, không thuộc trường hợp từ chối đăng ký kết hôn theo quy định, trường hợp tiếp nhận hồ sơ đăng ký kết hôn theo hình thức trực tiếp, thì công chức tư pháp - hộ tịch thực hiện việc ghi vào Sổ đăng ký kết hôn, cập nhật thông tin đăng ký kết hôn và lưu chính thức trên Phần mềm đăng ký, quản lý hộ tịch điện tử dùng chung.
                Trường hợp tiếp nhận hồ sơ đăng ký kết hôn theo hình thức trực tuyến, công chức tư pháp - hộ tịch gửi lại biểu mẫu Giấy chứng nhận kết hôn điện tử với thông tin đầy đủ cho người yêu cầu qua thư điện tử hoặc thiết bị số.
                Người yêu cầu có trách nhiệm kiểm tra tính chính xác, đầy đủ của các thông tin trên biểu mẫu Giấy chứng nhận kết hôn điện tử và xác nhận (tối đa một ngày).
                Nếu người có yêu cầu xác nhận thông tin đã thống nhất, đầy đủ hoặc không có phản hồi sau thời hạn yêu cầu thì công chức tư pháp - hộ tịch thực hiện việc ghi nội dung vào Sổ đăng ký kết hôn, cập nhật thông tin đăng ký kết hôn và lưu chính thức trên Phần mềm đăng ký, quản lý hộ tịch điện tử dùng chung.
                - Công chức tư pháp - hộ tịch in Giấy chứng nhận kết hôn, trình Lãnh đạo Ủy ban nhân dân cấp xã ký, chuyển tới Trung tâm Phục vụ hành chính công để trả kết quả cho người yêu cầu.
                - Người có yêu cầu đăng ký kết hôn (hai bên nam, nữ phải có mặt, xuất trình giấy tờ tuỳ thân để đối chiếu) kiểm tra thông tin trên Giấy chứng nhận kết hôn, trong Sổ đăng ký kết hôn, khẳng định sự tự nguyện kết hôn và ký tên vào Sổ đăng ký kết hôn, ký tên vào Giấy chứng nhận kết hôn, mỗi bên nam, nữ nhận 01 bản chính Giấy chứng nhận kết hôn.
            ',

            'coQuanThucHien'=>'Ủy ban Nhân dân xã, phường, thị trấn.',
            'trangThai'=>'Công khai',
            'yeuCauDieuKien'=>'
                - Nam từ đủ 20 tuổi trở lên, nữ từ đủ 18 tuổi trở lên;
                - Việc kết hôn do nam và nữ tự nguyện quyết định;
                - Các bên không bị mất năng lực hành vi dân sự;
                - Việc kết hôn không thuộc một trong các trường hợp cấm kết hôn, gồm:
                + Kết hôn giả tạo;
                + Tảo hôn, cưỡng ép kết hôn, lừa dối kết hôn, cản trở kết hôn;
                + Người đang có vợ, có chồng mà kết hôn với người khác hoặc chưa có vợ, chưa có chồng mà kết hôn với người đang có chồng, có vợ;
                + Kết hôn giữa những người cùng dòng máu về trực hệ; giữa những người có họ trong phạm vi ba đời; giữa cha, mẹ nuôi với con nuôi; giữa người đã từng là cha, mẹ nuôi với con nuôi, cha chồng với con dâu, mẹ vợ với con rể, cha dượng với con riêng của vợ, mẹ kế với con riêng của chồng.
                * Nhà nước không thừa nhận hôn nhân giữa những người cùng giới tính.
            ',
            'canCuPhapLy'=>'Luật 60/2014/QH13',
            'ketQuaThucHien'=>'Giấy chứng nhận kết hôn',
            ],


        ]);
    }
}
