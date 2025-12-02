@extends('admin.layout')

@section('title', 'Giấy xác nhận tiếp nhận hồ sơ')

@section('content')
<style>
    @media print {
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 13px;
        }

        .sidebar-menu, header, nav, .breadcrumb, .btn, .back-to-top {
            display: none !important;
        }

        #main-content, .wrapper, .invoice-wrapper, .invoice-card {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }

        .invoice-card {
            border: 1px solid #000 !important;
        }

        .invoice-header {
            border-bottom: 1px solid #000 !important;
        }

        .invoice-title {
            font-size: 18px !important;
        }
    }

    .invoice-card {
        border: 1px solid #dee2e6;
    }

    .invoice-header {
        border-bottom: 1px solid #dee2e6;
    }
</style>

<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <div class="invoice-wrapper">
                    <section class="panel invoice-card">
                        <header class="panel-heading invoice-header">
                            <div class="row">
                                <div class="col-xs-7">
                                    <h5 class="text-uppercase" style="color:#c0392b; font-weight:700;">
                                        ỦY BAN NHÂN DÂN PHƯỜNG ABC
                                    </h5>
                                    <h5 class="text-uppercase" style="font-weight:700;">
                                        BỘ PHẬN MỘT CỬA
                                    </h5>
                                    <small class="text-muted d-block">
                                        Địa chỉ: ......................................................................
                                    </small>
                                    <small class="text-muted d-block">
                                        Điện thoại: ..................................................................
                                    </small>
                                </div>
                                <div class="col-xs-5 text-right">
                                    <h5 class="text-uppercase" style="font-weight:700;">
                                        CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM
                                    </h5>
                                    <small class="text-muted d-block">
                                        Độc lập - Tự do - Hạnh phúc
                                    </small>
                                    <hr style="margin: 5px 0;">
                                    <small>
                                        Ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}
                                    </small>
                                </div>
                            </div>
                        </header>

                        <div class="panel-body">
                            <div class="text-center mb-3">
                                <h3 class="invoice-title text-uppercase" style="font-weight:700;">
                                    GIẤY XÁC NHẬN TIẾP NHẬN HỒ SƠ
                                </h3>
                                <p>
                                    Mã hồ sơ: <strong>{{ $hoSo->maHSXL }}</strong>
                                </p>
                            </div>

                            <p class="mb-1"><strong>I. Thông tin người nộp hồ sơ</strong></p>
                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-xs-6">
                                    <p>Họ tên: <strong>{{ $hoSo->tenChuHoSo ?? ($nguoi->hoTen ?? '................................') }}</strong></p>
                                </div>
                                <div class="col-xs-6">
                                    <p>Số điện thoại: <strong>{{ $hoSo->soDienThoai ?? ($nguoi->soDienThoai ?? '................') }}</strong></p>
                                </div>
                                <div class="col-xs-12">
                                    <p>Địa chỉ: <strong>{{ $nguoi->noiThuongTru ?? '................................................................................' }}</strong></p>
                                </div>
                            </div>

                            <p class="mb-1"><strong>II. Thông tin hồ sơ</strong></p>
                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-xs-12">
                                    <p>Tên thủ tục: <strong>{{ $tthc->tenTTHC ?? '............................................................' }}</strong></p>
                                </div>
                                <div class="col-xs-6">
                                    <p>Ngày tiếp nhận:
                                        <strong>
                                            {{ $hoSo->ngayTiepNhan ? \Carbon\Carbon::parse($hoSo->ngayTiepNhan)->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                                        </strong>
                                    </p>
                                </div>
                                <div class="col-xs-6">
                                    <p>Ngày hẹn trả:
                                        <strong>
                                            {{ $hoSo->ngayHenTra ? \Carbon\Carbon::parse($hoSo->ngayHenTra)->format('d/m/Y H:i') : '...............' }}
                                        </strong>
                                    </p>
                                </div>
                                <div class="col-xs-12">
                                    <p>Hình thức tiếp nhận:
                                        <strong>{{ $hoSo->hinhThuc == 'Nhận trực tuyến' ? 'Trực tuyến' : 'Trực tiếp' }}</strong>
                                    </p>
                                </div>
                            </div>

                            <p class="mb-1"><strong>III. Ghi chú</strong></p>
                            <p style="min-height: 60px;">
                                ..............................................................................................................................<br>
                                ..............................................................................................................................<br>
                                ..............................................................................................................................
                            </p>

                            <div class="row" style="margin-top: 30px;">
                                <div class="col-xs-6 text-center">
                                    <strong>Người nộp hồ sơ</strong><br>
                                    <small>(Ký, ghi rõ họ tên)</small>
                                </div>
                                <div class="col-xs-6 text-center">
                                    <strong>Cán bộ một cửa</strong><br>
                                    <small>(Ký, ghi rõ họ tên)</small><br><br>
                                    @php
                                        $canBo = \Illuminate\Support\Facades\Auth::user();
                                    @endphp
                                    <span style="font-size:18px;font-family:'Segoe Script','Brush Script MT',cursive;color:#c0392b;display:inline-block;">
                                        {{ $canBo->hoTen ?? '................' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="text-center no-print" style="margin-top: 15px;">
                        <button class="btn btn-info" onclick="window.print();">
                            <i class="fa fa-print"></i> In giấy xác nhận
                        </button>
                        <a href="{{ route('admin.hosoxuly.show', $hoSo->maHSXL) }}" class="btn btn-default">
                            Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>
@endsection


