@extends('layouts.app')

@section('title', 'Hướng dẫn sử dụng')


@section('content')
@section('content')
    @include('partials.support-header', ['breadcrumb' => 'Hướng Dẫn Sử Dụng'])
    <div class="container py-5">
        <div class="row g-4">
            <aside class="col-lg-3 support-sidebar">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Danh mục hỗ trợ</div>
                    <div class="list-group list-group-flush">
                        <a href="{{ url('/support/guide') }}" class="list-group-item list-group-item-action active">Hướng dẫn
                            chung</a>
                        <a href="{{ url('/support/faq') }}" class="list-group-item list-group-item-action">Các câu hỏi thường
                            gặp</a>
                    </div>
                </div>
            </aside>

            <main class="col-lg-9">
                <h1 class="main-title center">Hướng Dẫn Sử Dụng</h1>

                @php
                    // Thay các link video bên dưới bằng link YouTube (dạng embed) hoặc file mp4 của bạn
                    // Ví dụ YouTube embed: https://www.youtube.com/embed/VIDEO_ID
                    $videos = [
                        'step1' => 'https://dichvucong.gov.vn/general/1.mp4',
                        'step2' => 'https://dichvucong.gov.vn/general/2.mp4',
                        'step3' => 'https://dichvucong.gov.vn/general/3.mp4',
                        'step4' => 'https://dichvucong.gov.vn/general/4.mp4',
                    ];
                @endphp

                <div class="my-4">
                    <div class="position-relative mb-3">
                        <div class="border-top border-2 border-success position-absolute top-50 start-0 w-100"
                            style="z-index:0;"></div>
                        <div class="row text-center position-relative" style="z-index:1;">
                            <div class="col-3">
                                <div class="mx-auto rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center guide-step-circle"
                                    style="width:56px;height:56px;">01</div>
                                <div class="small mt-2 text-secondary">Tra cứu thủ tục hành chính, dịch vụ công</div>
                            </div>
                            <div class="col-3">
                                <div class="mx-auto rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center guide-step-circle"
                                    style="width:56px;height:56px;">02</div>
                                <div class="small mt-2 text-secondary">Chọn cơ quan thực hiện</div>
                            </div>
                            <div class="col-3">
                                <div class="mx-auto rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center guide-step-circle"
                                    style="width:56px;height:56px;">03</div>
                                <div class="small mt-2 text-secondary">Đăng ký, đăng nhập tài khoản</div>
                            </div>
                            <div class="col-3">
                                <div class="mx-auto rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center guide-step-circle"
                                    style="width:56px;height:56px;">04</div>
                                <div class="small mt-2 text-secondary">Nộp hồ sơ, tra cứu, theo dõi</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4 text-muted" style="color: #CE7A58; text-align: center; font-style: italic">Khuyến nghị: Hệ
                    thống chạy tốt nhất trên trình duyệt Chrome & Firefox</div>

                <div class="vstack gap-5">
                    <section>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-success text-white fw-bold d-inline-flex align-items-center justify-content-center me-3 guide-step-circle"
                                style="width:44px;height:44px;">01</div>
                            <h5 class="mb-0 fw-bold">Tra cứu thủ tục hành chính, dịch vụ công</h5>
                        </div>
                        <div class="row g-4 align-items-start">
                            <div class="col-md-7">
                                <ul class="mb-0">
                                    <li>Tìm kiếm theo từ khóa ở trang chủ, trang công dân, trang doanh nghiệp.</li>
                                    <li>Chọn thủ tục hành chính từ sự kiện của công dân, doanh nghiệp.</li>
                                    <li>Chọn từ danh sách dịch vụ công trực tuyến.</li>
                                </ul>
                            </div>
                            <div class="col-md-5">
                                @if (!empty($videos['step1']))
                                    <div class="ratio ratio-16x9">
                                        <iframe src="{{ $videos['step1'] }}" title="Video hướng dẫn 1"
                                            allowfullscreen></iframe>
                                    </div>
                                @else
                                    <div class="border rounded bg-light text-center py-5">Chưa có video — dán link vào biến
                                        $videos['step1']</div>
                                @endif
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-success text-white fw-bold d-inline-flex align-items-center justify-content-center me-3 guide-step-circle"
                                style="width:44px;height:44px;">02</div>
                            <h5 class="mb-0 fw-bold">Chọn cơ quan thực hiện</h5>
                        </div>
                        <div class="row g-4 align-items-start">
                            <div class="col-md-7">
                                <p class="mb-0">Căn cứ vào "Cơ quan thực hiện" trong thông tin thủ tục để lựa chọn nơi
                                    tiếp nhận phù hợp.</p>
                            </div>
                            <div class="col-md-5">
                                @if (!empty($videos['step2']))
                                    <div class="ratio ratio-16x9">
                                        <iframe src="{{ $videos['step2'] }}" title="Video hướng dẫn 2"
                                            allowfullscreen></iframe>
                                    </div>
                                @else
                                    <div class="border rounded bg-light text-center py-5">Chưa có video — dán link vào biến
                                        $videos['step2']</div>
                                @endif
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-success text-white fw-bold d-inline-flex align-items-center justify-content-center me-3 guide-step-circle"
                                style="width:44px;height:44px;">03</div>
                            <h5 class="mb-0 fw-bold">Đăng ký, đăng nhập tài khoản công dân, doanh nghiệp</h5>
                        </div>
                        <div class="row g-4 align-items-start">
                            <div class="col-md-7">
                                <p class="mb-0">Tạo tài khoản hoặc đăng nhập để bắt đầu nộp hồ sơ, thanh toán và theo dõi
                                    quá trình xử lý.</p>
                            </div>
                            <div class="col-md-5">
                                @if (!empty($videos['step3']))
                                    <div class="ratio ratio-16x9">
                                        <iframe src="{{ $videos['step3'] }}" title="Video hướng dẫn 3"
                                            allowfullscreen></iframe>
                                    </div>
                                @else
                                    <div class="border rounded bg-light text-center py-5">Chưa có video — dán link vào biến
                                        $videos['step3']</div>
                                @endif
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-success text-white fw-bold d-inline-flex align-items-center justify-content-center me-3 guide-step-circle"
                                style="width:44px;height:44px;">04</div>
                            <h5 class="mb-0 fw-bold">Nộp hồ sơ, tra cứu, theo dõi tình trạng hồ sơ</h5>
                        </div>
                        <div class="row g-4 align-items-start">
                            <div class="col-md-7">
                                <p class="mb-0">Sau khi nộp hồ sơ thành công, vào mục "Tra cứu hồ sơ" để xem chi tiết
                                    trạng thái xử lý theo mã hồ sơ.</p>
                            </div>
                            <div class="col-md-5">
                                @if (!empty($videos['step4']))
                                    <div class="ratio ratio-16x9">
                                        <iframe src="{{ $videos['step4'] }}" title="Video hướng dẫn 4"
                                            allowfullscreen></iframe>
                                    </div>
                                @else
                                    <div class="border rounded bg-light text-center py-5">Chưa có video — dán link vào biến
                                        $videos['step4']</div>
                                @endif
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
@endsection
{{-- mới --}}