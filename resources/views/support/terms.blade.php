@extends('layouts.app')

@section('title', 'Điều khoản sử dụng')

@section('content')
    @include('partials.support-header', ['breadcrumb' => 'Điều khoản sử dụng'])

    <div class="container py-5 terms-page">
        <h1 class="main-title -none center">
            Điều khoản và điều kiện sử dụng Cổng Dịch vụ công Quốc gia
        </h1>

        <p>
            Bằng việc sử dụng các Dịch vụ trên Cổng Dịch vụ công Quốc gia, tổ chức, cá nhân sử dụng mặc nhiên chấp thuận
            và cam kết thực hiện các điều khoản và điều kiện sử dụng sau đây:
        </p>
        <div class="divider-gray"></div>
        <h5 class="fw-bold text-brown mt-4">1. Trách nhiệm của tổ chức, cá nhân sử dụng dịch vụ</h5>
        <ul class="list-custom">
            <li>Chịu trách nhiệm trước pháp luật về những thông tin kê khai, đăng ký tài khoản trên Cổng Dịch vụ công Quốc gia; chỉ sử dụng Cổng cho các mục đích hợp pháp và chịu trách nhiệm về mọi hoạt động được thực hiện bằng tài khoản của mình.</li>
            <li>Giữ bí mật thông tin tài khoản, mật khẩu; nếu bị mất, đánh cắp hoặc có người sử dụng trái phép thì phải thông báo kịp thời cho cơ quan quản lý hệ thống.</li>
            <li>Chịu trách nhiệm đối với mọi nội dung do mình gửi, đăng ký, cung cấp khi sử dụng dịch vụ công và các tiện ích khác trên Cổng.</li>
            <li>Khi sử dụng lại các thông tin, nội dung từ Cổng để đăng tải ở nơi khác phải tuân thủ quy định của pháp luật.</li>
            <li>Đồng ý chia sẻ thông tin đăng ký trên Cổng Dịch vụ công Quốc gia theo quy định của hệ thống.</li>
        </ul>

        <h5 class="fw-bold text-brown mt-4">2. Các hành vi bị nghiêm cấm</h5>
        <ul class="list-custom">
            <li>Quấy rối, gây phiền toái, cản trở hoặc xâm phạm quyền và lợi ích hợp pháp của tổ chức, cá nhân khác.</li>
            <li>Đăng hoặc truyền tài liệu xuyên tạc, phỉ báng, khiêu dâm, xúc phạm, hoặc trái với quy định của pháp luật.</li>
            <li>Cản trở quá trình gửi, nhận, xử lý thông điệp dữ liệu.</li>
            <li>Thay đổi, sao chép, tiết lộ, xóa hoặc di chuyển trái phép dữ liệu.</li>
            <li>Tạo ra thông điệp dữ liệu hoặc chương trình tin học gây hại, phá hoại hạ tầng kỹ thuật.</li>
            <li>Gian lận, chiếm đoạt hoặc sử dụng trái phép chữ ký điện tử; công khai hóa thông tin riêng mà không được phép.</li>
            <li>Thực hiện các hành vi vi phạm pháp luật khác.</li>
        </ul>

        <h5 class="fw-bold text-brown mt-4">3. Quyền hạn của Cổng Dịch vụ công Quốc gia</h5>
        <p>
            Cổng Dịch vụ công Quốc gia có quyền tạm dừng, khóa, hủy các tài khoản vi phạm pháp luật, gian lận hoặc không tuân thủ
            các điều khoản sử dụng đã nêu mà không cần thông báo trước hoặc bồi thường.
        </p>

        <h5 class="fw-bold text-brown mt-4">4. Bảo mật thông tin</h5>
        <p>
            Cổng Dịch vụ công Quốc gia không chia sẻ thông tin của người sử dụng với cơ quan khác nếu không có sự cho phép,
            trừ các trường hợp:
        </p>
        <ul class="list-custom">
            <li>Việc cung cấp là cần thiết để giải quyết thủ tục hành chính, dịch vụ công theo yêu cầu của người sử dụng.</li>
            <li>Việc cung cấp thông tin là cần thiết vì lợi ích công cộng hoặc sức khỏe cộng đồng theo quy định pháp luật.</li>
        </ul>

        <h5 class="fw-bold text-brown mt-4">5. Sửa đổi điều khoản</h5>
        <p>
            Trong trường hợp nội dung điều khoản, điều kiện sử dụng được sửa đổi, hệ thống sẽ thông báo trên Cổng.
            Việc tiếp tục sử dụng đồng nghĩa với việc người dùng chấp nhận các thay đổi đó.
        </p>
    </div>
@endsection
