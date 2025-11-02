@extends('layouts.app')

@section('title', 'Giới thiệu')

@section('content')
    @include('partials.support-header', ['breadcrumb' => 'Giới thiệu'])

    <div class="container py-5 about-page">
        <h1 class="main-title -none">Giới thiệu về Cổng Dịch vụ công Quốc gia</h1>

        <p>
            Với quan điểm <strong>công khai, minh bạch</strong>, lấy người dân, doanh nghiệp làm trung tâm phục vụ,
            <strong>Cổng Dịch vụ công Quốc gia</strong> kết nối, cung cấp thông tin về thủ tục hành chính và dịch vụ công trực tuyến;
            hỗ trợ thực hiện, giám sát, đánh giá việc giải quyết thủ tục hành chính, dịch vụ công trực tuyến và tiếp nhận, xử lý phản ánh, kiến nghị của cá nhân, tổ chức trên toàn quốc.
        </p>

        <p>
            Cá nhân, tổ chức dễ dàng truy cập <strong>Cổng Dịch vụ công Quốc gia</strong> tại địa chỉ duy nhất
            <a href="https://dichvucong.gov.vn" target="_blank" class="text-success fw-semibold">www.dichvucong.gov.vn</a>,
            theo nhu cầu người dùng từ máy tính, máy tính bảng hoặc điện thoại di động kết nối internet để hưởng nhiều lợi ích như:
        </p>

        <div class="row text-center mt-5">
            @php
                $features = [
                    ['icon' => 'fa-arrow-right', 'text' => 'Đăng ký và được cấp ngay một tài khoản của Cổng dịch vụ công Quốc gia để đăng nhập;'],
                    ['icon' => 'fa-search', 'text' => 'Tra cứu thông tin, dịch vụ công các ngành, lĩnh vực, các địa phương tại Cơ sở dữ liệu quốc gia về thủ tục hành chính; Gửi phản ánh, kiến nghị liên quan đến việc giải quyết thủ tục hành chính, dịch vụ công;'],
                    ['icon' => 'fa-file-alt', 'text' => 'Đề nghị hỗ trợ thực hiện thủ tục hành chính, dịch vụ công qua <strong>Tổng đài điện thoại 18001096</strong> hoặc trực tuyến tại mục Hỗ trợ;'],
                    ['icon' => 'fa-list-check', 'text' => 'Theo dõi toàn bộ quá trình giải quyết thủ tục hành chính và xử lý phản ánh kiến nghị của mình bằng cách cung cấp mã hồ sơ, kể cả mã hồ sơ thủ tục hành chính không thực hiện qua Cổng DVCQG;'],
                    ['icon' => 'fa-share-nodes', 'text' => 'Đăng nhập bằng tài khoản Cổng DVCQG để đăng nhập các Cổng DVC của Bộ, ngành, địa phương; không cần cập nhật lại thông tin đã lưu;'],
                    ['icon' => 'fa-database', 'text' => 'Được hỗ trợ truy vấn thông tin cá nhân, tổ chức từ các Cơ sở dữ liệu đã tích hợp với Cổng DVCQG như đăng ký kinh doanh, thuế, bảo hiểm,…;'],
                    ['icon' => 'fa-building', 'text' => 'Thực hiện thủ tục hành chính tại nhiều tỉnh, thành phố chỉ cần khai báo 1 lần trên Cổng DVCQG;'],
                    ['icon' => 'fa-credit-card', 'text' => 'Sử dụng tài khoản ngân hàng, trung gian thanh toán để thanh toán trực tuyến phí, lệ phí;'],
                    ['icon' => 'fa-face-smile', 'text' => 'Đánh giá sự hài lòng trong giải quyết thủ tục hành chính.'],
                ];
            @endphp

            @foreach ($features as $feature)
                <div class="col-md-4 mb-5">
                    <div class="icon-box mb-3">
                        <i class="fa {{ $feature['icon'] }}"></i>
                    </div>
                    <p class="text-secondary">{!! $feature['text'] !!}</p>
                </div>
            @endforeach
        </div>

        <div class="highlight-box text-center my-5 p-4">
            <p class="mb-1">
                Các Bộ, ngành, địa phương nỗ lực cùng với sự tham gia tích cực của người dân và doanh nghiệp
                trong vận hành <strong>Cổng Dịch vụ công Quốc gia</strong> là góp phần xây dựng
                <strong>Chính phủ liêm chính, hành động, phát triển, phục vụ Nhân Dân.</strong>
            </p>
            <p class="fw-bold text-warning mb-0">
                Hãy truy cập <a href="https://www.dichvucong.gov.vn" target="_blank" class="text-decoration-none text-warning">www.dichvucong.gov.vn</a> !
            </p>
        </div>

        <h4 class="fw-bold text-dark mb-4">Lộ trình thực hiện</h4>

<div class="timeline-horizontal text-center mt-4">
    <div class="timeline-track"></div>

    <div class="row justify-content-center g-4">
        <div class="col-md-4 position-relative">
            <div class="timeline-dot bg-brown"></div>
            <h5 class="fw-bold text-brown">Năm 2019</h5>
            <p class="text-secondary small">
                Kết nối, tích hợp cổng DVCQG với cổng DVC và hệ thống một cửa điện tử
                các Bộ, ngành, địa phương để thí điểm cung cấp một số dịch vụ công trực tuyến như:
                Cấp đổi giấy phép lái xe, cấp giấy phép lái xe quốc tế, thông báo thực hiện khuyến mại,
                đăng ký hoạt động khuyến mại, cấp điện mới từ lưới điện trung áp, cấp điện mới từ lưới điện hạ áp...
            </p>
        </div>

        <div class="col-md-4 position-relative">
            <div class="timeline-dot bg-brown"></div>
            <h5 class="fw-bold text-brown">Năm 2020</h5>
            <p class="text-secondary small">
                Tích hợp tối thiểu 30% các dịch vụ công trực tuyến thiết yếu.
            </p>
        </div>

        <div class="col-md-4 position-relative">
            <div class="timeline-dot bg-brown"></div>
            <h5 class="fw-bold text-brown">Sau năm 2020</h5>
            <p class="text-secondary small">
                Tăng dần mỗi năm tích hợp 20% dịch vụ công trực tuyến mức độ 3, 4
                của các Bộ, ngành, địa phương.
            </p>
        </div>
    </div>
</div>
@endsection
{{-- mới --}}
