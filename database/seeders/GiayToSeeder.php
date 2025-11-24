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

                'tenGiayTo' => 'Tờ khai đăng ký kết hôn theo mẫu, có đủ thông tin của hai bên nam, nữ. Hai bên nam, nữ có thể khai chung vào một Tờ khai đăng ký kết hôn (nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tiếp);',
                'loaiGiayTo' =>'Tờ khai'
            ],
            [

                'tenGiayTo' => '- Mẫu hộ tịch điện tử tương tác đăng ký kết hôn (do người yêu cầu cung cấp thông tin theo hướng dẫn trên Cổng dịch vụ công, nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tuyến)',
                'loaiGiayTo' =>'Mẫu đơn'
            ],
            [

                'tenGiayTo' => '- Người có yêu cầu đăng ký kết hôn thực hiện việc nộp/xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến) các giấy tờ sau:',
                'loaiGiayTo' =>'Mẫu đơn'
            ],
                        [

                'tenGiayTo' => '- Hộ chiếu/Chứng minh nhân dân/Thẻ căn cước công dân/Thẻ căn cước/Căn cước điện tử/Giấy chứng nhận căn cước hoặc các giấy tờ khác có dán ảnh và thông tin cá nhân do cơ quan có thẩm quyền cấp, còn giá trị sử dụng để chứng minh về nhân thân của người có yêu cầu đăng ký kết hôn. Trường hợp các thông tin cá nhân trong các giấy tờ này đã có trong CSDLQGVDC, CSDLHTĐT, được hệ thống điền tự động thì không phải tải lên (theo hình thức trực tuyến);',
                'loaiGiayTo' =>'Tờ khai'
            ],
            [

                'tenGiayTo' => '- Giấy tờ có giá trị chứng minh thông tin về cư trú trong trường hợp cơ quan đăng ký hộ tịch không thể khai thác được thông tin về nơi cư trú của công dân theo các phương thức quy định tại khoản 2 Điều 14 Nghị định số 104/2022/NĐ-CP ngày 21/12/2022 của Chính phủ. Trường hợp các thông tin về giấy tờ chứng minh nơi cư trú đã được khai thác từ Cơ sở dữ liệu quốc gia về dân cư bằng các phương thức này thì người có yêu cầu không phải xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến).',
                'loaiGiayTo' =>'Tờ khai'
            ],
//=================Thủ tục đăng ký kết hôn
            [

                'tenGiayTo' => '- Hộ chiếu/Chứng minh nhân dân/Thẻ căn cước công dân/Thẻ căn cước/Căn cước điện tử/Giấy chứng nhận căn cước hoặc các giấy tờ khác có dán ảnh và thông tin cá nhân do cơ quan có thẩm quyền cấp, còn giá trị sử dụng để chứng minh về nhân thân của người có yêu cầu đăng ký kết hôn. Trường hợp các thông tin cá nhân trong các giấy tờ này đã có trong CSDLQGVDC, CSDLHTĐT, được hệ thống điền tự động thì không phải tải lên (theo hình thức trực tuyến);',
                'loaiGiayTo' =>'Giấy tờ cá nhân'
            ],
            [

                'tenGiayTo' => '- Giấy tờ có giá trị chứng minh thông tin về cư trú trong trường hợp cơ quan đăng ký hộ tịch không thể khai thác được thông tin về nơi cư trú của công dân theo các phương thức quy định tại khoản 2 Điều 14 Nghị định số 104/2022/NĐ-CP ngày 21/12/2022 của Chính phủ. Trường hợp các thông tin về giấy tờ chứng minh nơi cư trú đã được khai thác từ Cơ sở dữ liệu quốc gia về dân cư bằng các phương thức này thì người có yêu cầu không phải xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến).',
                'loaiGiayTo' =>'Giấy tờ cá nhân'
            ],
            [

                'tenGiayTo' => '- Cá nhân có quyền lựa chọn thực hiện thủ tục hành chính về hộ tịch tại cơ quan đăng ký hộ tịch nơi cư trú; nơi cư trú của cá nhân được xác định theo quy định của pháp luật về cư trú. Trường hợp cá nhân lựa chọn thực hiện thủ tục hành chính về hộ tịch không phải tại Ủy ban nhân dân cấp xã nơi thường trú hoặc nơi tạm trú thì Ủy ban nhân dân cấp xã nơi tiếp nhận yêu cầu có trách nhiệm hỗ trợ người dân nộp hồ sơ đăng ký hộ tịch trực tuyến đến đúng cơ quan có thẩm quyền theo quy định.	',
                'loaiGiayTo' =>'Giấy tờ cá nhân'
            ],
            [
                'tenGiayTo'=>'- Đối với yêu cầu đăng ký kết hôn, cơ quan đăng ký hộ tịch tra cứu thông tin về tình trạng hôn nhân của người yêu cầu đăng ký kết hôn trên Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh thông qua kết nối với Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư. Kết quả tra cứu được lưu trữ dưới dạng điện tử hoặc bản giấy, phản ánh đầy đủ, chính xác thông tin tại thời điểm tra cứu và đính kèm hồ sơ của người đăng ký. Trường hợp không tra cứu được tình trạng hôn nhân do chưa có thông tin trong Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư, thì cơ quan đăng ký hộ tịch đề nghị Ủy ban nhân dân cấp xã nơi người yêu cầu thường trú/nơi đã đăng ký kết hôn xác minh, cung cấp thông tin. Trong thời hạn 03 ngày làm việc kể từ ngày nhận được yêu cầu xác minh, Ủy ban nhân dân cấp xã nơi nhận được đề nghị xác minh có trách nhiệm kiểm tra, xác minh và gửi kết quả về tình trạng hôn nhân của người đó',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Nếu bên kết hôn là công dân Việt Nam đã ly hôn hoặc hủy việc kết hôn tại cơ quan có thẩm quyền nước ngoài nhưng qua tra cứu thông tin trong Cơ sở dữ liệu hộ tịch điện tử; thông qua kết nối giữa Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh với Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư không thể hiện thông tin về việc đã ghi chú ly hôn, hủy việc kết hôn thì cơ quan đăng ký hộ tịch hướng dẫn công dân thực hiện thủ tục ghi vào sổ hộ tịch việc ly hôn/hủy việc kết hôn tại cơ quan nhà nước có thẩm quyền trước khi giải quyết việc đăng ký kết hôn.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Đối với giấy tờ nộp, xuất trình nếu người yêu cầu nộp hồ sơ theo hình thức trực tiếp:',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Người yêu cầu đăng ký hộ tịch có thể nộp bản sao được chứng thực từ bản chính hoặc bản sao được cấp từ sổ gốc (sau đây gọi là bản sao) hoặc bản chụp kèm theo bản chính, bản điện tử các giấy tờ này, bao gồm cả giấy tờ được tích hợp, hiển thị trên Ứng dụng định danh điện tử (VneID).',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'Trường hợp người yêu cầu nộp bản chụp kèm theo bản chính giấy tờ thì người tiếp nhận có trách nhiệm kiểm tra, đối chiếu bản chụp với bản chính và ký xác nhận, không được yêu cầu nộp bản sao giấy tờ đó.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Đối với giấy tờ xuất trình khi đăng ký hộ tịch, người tiếp nhận có trách nhiệm kiểm tra, đối chiếu với thông tin trong tờ khai, chụp lại hoặc ghi lại thông tin để lưu trong hồ sơ và trả lại cho người xuất trình, không được yêu cầu nộp bản sao hoặc bản chụp giấy tờ đó.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Người tiếp nhận có trách nhiệm tiếp nhận đúng, đủ hồ sơ đăng ký hộ tịch theo quy định của pháp luật hộ tịch, không được yêu cầu người đăng ký hộ tịch nộp thêm giấy tờ mà pháp luật hộ tịch không quy định phải nộp .	',
                'loaiGiayTo'=>'Tờ khai'
            ],

            [

                'tenGiayTo'=>'+ Người tiếp nhận hồ sơ thực hiện khai thác thông tin trong CSDLQGVDC theo quy định pháp luật nếu người yêu cầu đăng ký hộ tịch đã cung cấp họ, chữ đệm, tên; ngày, tháng, năm sinh; số định danh cá nhân/thẻ căn cước công dân/thẻ căn cước/căn cước điện tử. Trường hợp các thông tin cần khai thác không có trong CSDLQGVDC thì đề nghị người yêu cầu kê khai đầy đủ.',
                'loaiGiayTo'=>'Tờ khai'
            ],

            [

                'tenGiayTo'=>'- Đối với giấy tờ gửi kèm theo nếu người yêu cầu nộp hồ sơ theo hình thức trực tuyến:',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Bản chụp các giấy tờ gửi kèm theo hồ sơ đăng ký kết hôn trực tuyến phải bảo đảm rõ nét, đầy đủ, toàn vẹn về nội dung, là bản chụp bằng máy ảnh, điện thoại hoặc được chụp, được quét bằng thiết bị điện tử, từ giấy tờ được cấp hợp lệ, còn giá trị sử dụng.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Trường hợp giấy tờ, tài liệu phải gửi kèm trong hồ sơ đăng ký kết hôn trực tuyến đã có bản sao điện tử hoặc đã có bản điện tử giấy tờ hộ tịch thì người yêu cầu được sử dụng bản điện tử này.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Giấy tờ do cơ quan có thẩm quyền của nước ngoài cấp, công chứng hoặc xác nhận để sử dụng cho việc đăng ký hộ tịch tại Việt Nam phải được hợp pháp hóa lãnh sự theo quy định của pháp luật, trừ trường hợp được miễn theo điều ước quốc tế mà Việt Nam là thành viên.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Trường hợp người yêu cầu đăng ký kết hôn không cung cấp được giấy tờ nêu trên theo quy định hoặc giấy tờ nộp, xuất trình bị tẩy xóa, sửa chữa, làm giả thì cơ quan đăng ký hộ tịch có thẩm quyền hủy bỏ kết quả đăng ký kết hôn.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Tờ khai đăng ký kết hôn theo mẫu, có đủ thông tin của hai bên nam, nữ. Hai bên nam, nữ có thể khai chung vào một Tờ khai đăng ký kết hôn (nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tiếp);',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Mẫu hộ tịch điện tử tương tác đăng ký kết hôn (do người yêu cầu cung cấp thông tin theo hướng dẫn trên Cổng dịch vụ công, nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tuyến)',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Người có yêu cầu đăng ký kết hôn thực hiện việc nộp/xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến) các giấy tờ sau:',
                'loaiGiayTo'=>'Tờ khai'
            ],

            //=================Cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh
            [

                'tenGiayTo'=>'Văn bản ủy quyền theo quy định của pháp luật trong trường hợp ủy quyền thực hiện yêu cầu cấp bản sao Trích lục hộ tịch. Trường hợp người được ủy quyền là ông, bà, cha, mẹ, con, vợ, chồng, anh, chị, em ruột của người ủy quyền thì văn bản ủy quyền không phải chứng thực.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'Trường hợp gửi hồ sơ qua hệ thống bưu chính thì phải gửi kèm theo bản sao có chứng thực các giấy tờ phải xuất trình nêu trên.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Hộ chiếu/Chứng minh nhân dân/Thẻ căn cước công dân/Thẻ căn cước/Căn cước điện tử/Giấy chứng nhận căn cước hoặc các giấy tờ khác có dán ảnh và thông tin cá nhân do cơ quan có thẩm quyền cấp, còn giá trị sử dụng để chứng minh về nhân thân của người có yêu cầu cấp bản sao Trích lục hộ tịch. Trường hợp các thông tin cá nhân trong các giấy tờ này đã có trong CSDLQGVDC, CSDLHTĐT, được hệ thống điền tự động thì không phải tải lên (theo hình thức trực tuyến).',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Cá nhân có quyền lựa chọn thực hiện thủ tục hành chính về hộ tịch tại cơ quan đăng ký hộ tịch nơi cư trú; nơi cư trú của cá nhân được xác định theo quy định của pháp luật về cư trú. Trường hợp cá nhân lựa chọn thực hiện thủ tục hành chính về hộ tịch không phải tại Ủy ban nhân dân cấp xã nơi thường trú hoặc nơi tạm trú thì Ủy ban nhân dân cấp xã nơi tiếp nhận yêu cầu có trách nhiệm hỗ trợ người dân nộp hồ sơ đăng ký hộ tịch trực tuyến đến đúng cơ quan có thẩm quyền theo quy định.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Đối với giấy tờ nộp, xuất trình nếu người yêu cầu nộp hồ sơ theo hình thức trực tiếp:',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Người yêu cầu đăng ký hộ tịch có thể nộp bản sao được chứng thực từ bản chính hoặc bản sao được cấp từ sổ gốc (sau đây gọi là bản sao) hoặc bản chụp kèm theo bản chính, bản điện tử các giấy tờ này, bao gồm cả giấy tờ được tích hợp, hiển thị trên Ứng dụng định danh điện tử (VneID).',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'Trường hợp người yêu cầu nộp bản chụp kèm theo bản chính giấy tờ thì người tiếp nhận có trách nhiệm kiểm tra, đối chiếu bản chụp với bản chính và ký xác nhận, không được yêu cầu nộp bản sao giấy tờ đó.	',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Đối với giấy tờ xuất trình khi đăng ký hộ tịch, người tiếp nhận có trách nhiệm kiểm tra, đối chiếu với thông tin trong tờ khai, chụp lại hoặc ghi lại thông tin để lưu trong hồ sơ và trả lại cho người xuất trình, không được yêu cầu nộp bản sao hoặc bản chụp giấy tờ đó.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Người tiếp nhận có trách nhiệm tiếp nhận đúng, đủ hồ sơ đăng ký hộ tịch theo quy định của pháp luật hộ tịch, không được yêu cầu người đăng ký hộ tịch nộp thêm giấy tờ mà pháp luật hộ tịch không quy định phải nộp .',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Người tiếp nhận hồ sơ thực hiện khai thác thông tin trong CSDLQGVDC theo quy định pháp luật nếu người yêu cầu đăng ký hộ tịch đã cung cấp họ, chữ đệm, tên; ngày, tháng, năm sinh; số định danh cá nhân/thẻ căn cước công dân/thẻ căn cước/căn cước điện tử. Trường hợp các thông tin cần khai thác không có trong CSDLQGVDC thì đề nghị người yêu cầu kê khai đầy đủ.	',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Đối với giấy tờ gửi kèm theo nếu người yêu cầu nộp hồ sơ theo hình thức trực tuyến:',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Bản chụp các giấy tờ gửi kèm theo hồ sơ cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tuyến phải bảo đảm rõ nét, đầy đủ, toàn vẹn về nội dung, là bản chụp bằng máy ảnh, điện thoại hoặc được chụp, được quét bằng thiết bị điện tử, từ giấy tờ được cấp hợp lệ, còn giá trị sử dụng.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Trường hợp giấy tờ, tài liệu phải gửi kèm trong hồ sơ cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tuyến đã có bản sao điện tử hoặc bản điện tử giấy tờ hộ tịch thì người yêu cầu được sử dụng bản điện tử này.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Trường hợp người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh không cung cấp đầy đủ hoặc cung cấp các thông tin không chính xác, không thể tra cứu được thông tin thì cơ quan đăng ký hộ tịch từ chối giải quyết.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Giấy tờ do cơ quan có thẩm quyền của nước ngoài cấp, công chứng hoặc xác nhận để sử dụng cho việc đăng ký hộ tịch tại Việt Nam phải được hợp pháp hóa lãnh sự theo quy định của pháp luật, trừ trường hợp được miễn theo điều ước quốc tế mà Việt Nam là thành viên.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Trường hợp người yêu cầu đăng ký hộ tịch lựa chọn nhận kết quả tại Trung tâm Phục vụ hành chính công thì người yêu cầu đăng ký hộ tịch phải xuất trình giấy tờ tuỳ thân, nộp các giấy tờ là thành phần hồ sơ theo quy định.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Trường hợp người yêu cầu đăng ký hộ tịch không lựa chọn nhận kết quả tại Trung tâm Phục vụ hành chính công thì người yêu cầu đăng ký hộ tịch nộp các giấy tờ là thành phần hồ sơ theo quy định trước khi nhận kết quả.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Tờ khai đề nghị bản sao Trích lục hộ tịch theo mẫu trong trường hợp người yêu cầu là cá nhân hoặc Văn bản yêu cầu cấp bản sao Trích lục hộ tịch nêu rõ lý do trong trường hợp người yêu cầu là cơ quan, tổ chức (nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tiếp);',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Mẫu điện tử tương tác yêu cầu cấp bản sao Giấy khai sinh, bản sao Trích lục hộ tịch (do người yêu cầu cung cấp thông tin theo hướng dẫn trên Cổng dịch vụ công, nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tuyến)',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'- Người có yêu cầu cấp bản sao Trích lục hộ tịch thực hiện việc nộp/xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến) các giấy tờ sau:',
                'loaiGiayTo'=>'Tờ khai'
            ],


            //======Thủ tục chứng thực chữ ký trong các giấy tờ, văn bản (áp dụng cho cả trường hợp chứng thực điểm chỉ và trường hợp người yêu cầu chứng thực không thể ký, không thể điểm chỉ được)
            [

                'tenGiayTo'=>'+ Bản chính hoặc bản sao có chứng thực Thẻ căn cước công dân/Thẻ căn cước/Giấy chứng nhận căn cước/Hộ chiếu/giấy tờ xuất nhập cảnh/giấy tờ có giá trị đi lại quốc tế còn giá trị sử dụng hoặc Căn cước điện tử.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'+ Giấy tờ, văn bản mà mình sẽ yêu cầu chứng thực chữ ký. Trường hợp chứng thực chữ ký trong giấy tờ, văn bản bằng tiếng nước ngoài, nếu người thực hiện chứng thực không hiểu rõ nội dung của giấy tờ, văn bản thì có quyền yêu cầu người yêu cầu chứng thực nộp kèm theo bản dịch ra tiếng Việt nội dung của giấy tờ, văn bản đó (bản dịch không cần công chứng hoặc chứng thực chữ ký người dịch, người yêu cầu chứng thực phải chịu trách nhiệm về nội dung của bản dịch). + Người yêu cầu chứng thực nhận kết quả tại nơi nộp hồ sơ.',
                'loaiGiayTo'=>'Tờ khai'
            ],

            //===============Chứng thực bản sao từ bản chính giấy tờ, văn bản do cơ quan, tổ chức có thẩm quyền của Việt Nam; cơ quan, tổ chức có thẩm quyền của nước ngoài; cơ quan, tổ chức có thẩm quyền của Việt Nam liên kết với cơ quan, tổ chức có thẩm quyền của nước ngoài cấp hoặc chứng nhận
            [

                'tenGiayTo'=>'Bản chính giấy tờ, văn bản làm cơ sở để chứng thực bản sao và bản sao cần chứng thực. Trường hợp người yêu cầu chứng thực chỉ xuất trình bản chính thì cơ quan, tổ chức tiến hành chụp từ bản chính để thực hiện chứng thực, trừ trường hợp cơ quan, tổ chức không có phương tiện để chụp. Bản sao từ bản chính để thực hiện chứng thực phải có đầy đủ các trang đã ghi thông tin của bản chính.',
                'loaiGiayTo'=>'Tờ khai'
            ],

                //===============Xác nhận thông tin về cư trú
            [

                'tenGiayTo'=>'Tờ khai thay đổi thông tin cư trú (Mẫu CT01 ban hành kèm theo Thông tư số 66/2023/TT-BCA).',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'*Lưu ý: Trường hợp thực hiện đăng ký cư trú trực tuyến, người yêu cầu đăng ký cư trú khai báo thông tin theo biểu mẫu điện tử được cung cấp sẵn.',
                'loaiGiayTo'=>'Tờ khai'
            ],


    //==============Xóa đăng ký tạm trú

            [

                'tenGiayTo'=>'- Tờ khai thay đổi thông tin cư trú (Mẫu CT01 ban hành kèm theo Thông tư số 66/2023/TT-BCA);',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'Giấy tờ, tài liệu chứng minh thuộc một trong các trường hợp xóa đăng ký thường trú quy định tại Điều 24 Luật Cư trú; trừ trường hợp thông tin chứng minh thuộc trường hợp xóa đăng ký thường trú của công dân đã có trong Cơ sở dữ liệu chuyên ngành được kết nối, chia sẻ với cơ quan đăng ký cư trú thì cơ quan đăng ký cư trú tự kiểm tra, xác minh, không yêu cầu công dân phải cung cấp giấy tờ chứng minh.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'*Lưu ý: Việc nộp hồ sơ đăng ký cư trú - Trường hợp nộp hồ sơ trực tiếp tại cơ quan đăng ký cư trú thì người yêu cầu đăng ký cư trú có thể nộp bản sao giấy tờ, tài liệu được chứng thực từ bản chính hoặc bản sao giấy tờ được cấp từ sổ gốc (sau đây gọi là bản sao) hoặc bản quét, bản chụp kèm theo bản chính giấy tờ, tài liệu để đối chiếu. Trường hợp người yêu cầu đăng ký cư trú nộp bản quét hoặc bản chụp kèm theo bản chính giấy tờ để đối chiếu thì người tiếp nhận có trách nhiệm kiểm tra, đối chiếu bản quét, bản chụp với bản chính và ký xác nhận, không được yêu cầu nộp bản sao giấy tờ đó. - Trường hợp thực hiện đăng ký cư trú trực tuyến, người yêu cầu đăng ký cư trú khai báo thông tin theo biểu mẫu điện tử được cung cấp sẵn, đăng tải bản quét hoặc bản chụp giấy tờ, tài liệu hợp lệ (không bắt buộc phải công chứng, chứng thực, ký số hoặc xác thực bằng hình thức khác) hoặc dẫn nguồn tài liệu từ Kho quản lý dữ liệu điện tử của tổ chức, cá nhân. Trường hợp công dân đăng tải bản quét, bản chụp giấy tờ, tài liệu mà không được ký số hoặc xác thực bằng hình thức khác thì khi cơ quan đăng ký cư trú tiến hành kiểm tra, xác minh để giải quyết thủ tục về cư trú; công dân có trách nhiệm xuất trình giấy tờ, tài liệu đã đăng tải để cơ quan đăng ký cư trú kiểm tra, đối chiếu và ghi nhận tính chính xác vào biên bản xác minh. Cơ quan đăng ký cư trú không yêu cầu công dân nộp để lưu giữ giấy tờ đó. - Bản quét hoặc bản chụp giấy tờ bằng thiết bị điện tử từ giấy tờ được cấp hợp lệ, còn giá trị sử dụng phải bảo đảm rõ nét, đầy đủ, toàn vẹn về nội dung; đã được hợp pháp hóa lãnh sự, dịch sang tiếng Việt theo quy định nếu là giấy tờ do cơ quan có thẩm quyền nước ngoài cấp trừ trường hợp được miễn hợp pháp hóa lãnh sự. - Trường hợp thông tin giấy tờ chứng minh điều kiện đăng ký cư trú đã được chia sẻ và khai thác từ cơ sở dữ liệu quốc gia, cơ sở dữ liệu chuyên ngành thì cơ quan đăng ký cư trú không được yêu cầu công dân nộp, xuất trình giấy tờ đó để giải quyết đăng ký cư trú.',
                'loaiGiayTo'=>'Tờ khai'
            ],

        //============Cấp Phiếu lý lịch tư pháp cho công dân Việt Nam, người nước ngoài đang cư trú tại Việt Nam
            [

                'tenGiayTo'=>'Tờ khai yêu cầu cấp Phiếu lý lịch tư pháp theo mẫu quy định tại Thông tư 06/2024/TT-BTP (Mẫu số 03/2024/LLTP; Mẫu số 04/2024/LLTP; Mẫu số 12/2024/LLTP; Mẫu số 13/2024/LLTP)',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'Bản sao Chứng minh nhân dân hoặc Thẻ căn cước hoặc Thẻ Căn cước công dân hoặc hộ chiếu của người được cấp Phiếu lý lịch tư pháp (Trường hợp nộp bản chụp thì phải xuất trình bản chính để đối chiếu. Trường hợp không có bản chính để đối chiếu thì nộp bản sao có chứng thực theo quy định của pháp luật). Trường hợp người có yêu cầu cấp Phiếu lý lịch tư pháp nộp hồ sơ bằng hình thức trực tuyến trên Cổng dịch vụ công quốc gia hoặc Hệ thống thông tin giải quyết thủ tục hành chính Bộ Tư pháp thì không cần đính kèm bản sao Chứng minh nhân dân hoặc Thẻ Căn cước hoặc Thẻ Căn cước công dân hoặc hộ chiếu.	',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'Văn bản ủy quyền trong trường hợp ủy quyền cho người khác làm thủ tục yêu cầu cấp Phiếu lý lịch tư pháp số 1 (trường hợp người được ủy quyền là cha, mẹ, vợ, chồng, con của người ủy quyền thì không cần văn bản ủy quyền). Văn bản ủy quyền phải được công chứng, chứng thực theo quy định của pháp luật Việt Nam. Cá nhân yêu cầu cấp Phiếu lý lịch tư pháp số 2 không được ủy quyền cho người khác làm thủ tục yêu cầu cấp Phiếu lý lịch tư pháp. Ngoài ra, người yêu cầu cấp Phiếu lý lịch tư pháp thuộc đối tượng được miễn hoặc giảm phí phải xuất trình các giấy tờ để chứng minh.',
                'loaiGiayTo'=>'Tờ khai'
            ],
            [

                'tenGiayTo'=>'Lưu ý: + Trường hợp người có yêu cầu cấp Phiếu lý lịch tư pháp nộp hồ sơ trực tuyến trên Cổng dịch vụ công quốc gia hoặc Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh thì sử dụng Tờ khai yêu cầu cấp Phiếu lý lịch tư pháp điện tử tương tác theo Mẫu số 12/2024/LLTP; Mẫu số 13/2024/LLTP. + Người được ủy quyền phải xuất trình Chứng minh nhân dân hoặc Thẻ Căn cước hoặc Thẻ Căn cước công dân hoặc Hộ chiếu khi thực hiện thủ tục yêu cầu cấp Phiếu lý lịch tư pháp.',
                'loaiGiayTo'=>'Tờ khai'
            ],


            //=============Liên thông các thủ tục hành chính về đăng ký khai sinh, cấp Thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi\
            // maGiayTo = 57
            [
                'tenGiayTo' =>'- Tờ khai đăng ký khai sinh theo mẫu quy định.',
                'loaiGiayTo' => 'Tờ khai'
            ],
            [
                'tenGiayTo' =>'- Giấy chứng sinh do cơ sở y tế nơi trẻ em sinh ra cấp; nếu trẻ em sinh ra ngoài cơ sở y tế thì giấy chứng sinh được thay bằng văn bản xác nhận của người làm chứng; trường hợp không có người làm chứng thì người đi khai sinh phải làm giấy cam đoan về việc sinh là có thực. Đối với trường hợp trẻ em bị bỏ rơi thì nộp biên bản về việc trẻ em bị bỏ rơi thay cho giấy chứng sinh.',
                'loaiGiayTo' =>'Tờ khai'
            ],
            [
                'tenGiayTo' =>'- Tờ khai tham gia bảo hiểm y tế, Danh sách đề nghị cấp thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi của Ủy ban nhân dân cấp xã (theo mẫu quy định)',
                'loaiGiayTo' =>'Tờ khai'
            ]
        ]);
    }
}
