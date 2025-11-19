@extends('layouts.app')
@section('title', 'Chi tiết thủ tục')
@section('content')

<!---Page header-->
<div class="container-fluid page-header pt-5">
    <div class="container py-5">
        <form class="d-flex wow fadeInUp" data-wow-delay="0.3s" role="search">
            <input class="form-control me-2" type="search" placeholder="Nhập từ khóa tìm kiếm" aria-label="Search">
            <button class="btn btn-primary" type="submit">TÌM KIẾM</button>
        </form>
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">TRANG CHỦ</a></li>
                <li class="breadcrumb-item"><a class="text-white" href="#">DỊCH VỤ CÔNG NỔI BẬT</a></li>
            </ol>
        </nav>
    </div>
</div>
<!--Page header-->

<!--Thông tin thủ tục hành chính-->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-9 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-items rounded h-100 p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">


                        <div class="col-8">
                        <h3 class="fw-semibold text-uppercase">
                            <i class="fa-solid fa-file-lines h-100 w-100 text-primary"></i>{{ $tthc->tenTTHC }}
                        </h3>
                        </div>
                        <div class="col-4 justify-content-end d-flex">
                        <button type="button" class="btn btn-primary shadow-sm" onclick="openCustomModal()">
                            <i class="fa-solid fa-circle-info me-1"></i> Xem thông tin chi tiết
                        </button>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <div class="frame-service-detail p-3 rounded border h-100">
                                <h6 class="mb-2 text-uppercase ">Trình tự thực hiện</h6>
                                <div class="small text-justify"style="text-align: justify; ">{!! nl2br(e($tthc->trinhTuThucHien)) !!}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="mb-2 text-uppercase ">Cách thực hiện</h6>
                        <div class="table-responsive rounded mb-0">
                            <table class="table-service-cth table table-sm table-striped  rounded table-bordered align-middle mb-0"style="text-align: justify ">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kênh</th>
                                            <th >Thời hạn giải quyết</th>
                                            <th >Mô tả phí/lệ phí</th>
                                            <th>Mô tả</th>
                                        </tr>
                                    </thead>
                                    <tbody ">
                                        @forelse($cachThucHiens as $cth)
                                            <tr>
                                                <td>{{ $cth->kenh }}</td>
                                                <td>{!! nl2br(e($cth->thoiHanGiaiQuyet)) !!}</td>
                                                <td>{!! nl2br(e($cth->moTaPhiLePhi)) !!}</td>
                                                <td>{!! nl2br(e($cth->moTa)) !!}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">—</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="mb-4">
                        <h6 class="mb-2 text-uppercase">Thành phần hồ sơ</h6>
                        <div class="accordion" id="thanhPhanAccordion">
                            @forelse($thanhPhanHoSos as $tenThanhPhan => $giayTos)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                        <button class="accordion-button bg-primary text-white {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $loop->index }}">
                                            {{ $tenThanhPhan }}
                                            <span class="badge bg-dark ms-2">{{ $giayTos->count() }} giấy tờ</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $loop->index }}"
                                         class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                         aria-labelledby="heading{{ $loop->index }}"
                                         data-bs-parent="#thanhPhanAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm  table-bordered align-middle mb-0">
                                                    <thead class="table-dark">
                                                        <tr class="py-3 bg-dark">
                                                            <th style="width: 60px">#</th>
                                                            <th>Tên giấy tờ</th>
                                                            <th style="width: 120px">Bản chính</th>
                                                            <th style="width: 120px">Bản sao</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-dark">
                                                        @foreach($giayTos as $index => $tp)
                                                            @if($tp->tenGiayTo)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $tp->tenGiayTo }}</td>
                                                                    <td class="text-center">
                                                                        @if($tp->soLuongBanChinh)
                                                                            <span class="badge bg-success">{{ $tp->soLuongBanChinh }}</span>
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($tp->soLuongBanSao)
                                                                            <span class="badge bg-info">{{ $tp->soLuongBanSao }}</span>
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">Chưa có thông tin thành phần hồ sơ</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="row g-4">

                        <div class="col-md-12">
                            <div class="frame-service-detail p-3 rounded border h-100">
                                <h6 class="mb-2 text-uppercase">Cơ quan thực hiện</h6>
                                <div class="small" style="text-align: justify;" >{{ $tthc->coQuanThucHien }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="frame-service-detail p-3 rounded border">
                                <h6 class="mb-2 text-uppercase">Yêu cầu, điều kiện</h6>
                                <div class="small text-justify" style="text-align: justify;">{!! nl2br(e($tthc->yeuCauDieuKien)) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 wow fadeInUp" data-wow-delay="0.3s"">
                <div class="" style="top: 100px;">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body">
                            <h6 class="card-title text-uppercase mb-3">Thực hiện thủ tục</h6>
                            <div class="d-grid gap-2">
                                @auth
                                    <a href="{{ route('appointment', $tthc->maTTHC) }}" class="btn btn-outline-primary">
                                        <i class="fa-regular fa-calendar-check me-1"></i> Đặt lịch nộp hồ sơ
                                    </a>
                                @else
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <i class="fa-regular fa-calendar-check me-1"></i> Đặt lịch nộp hồ sơ
                                    </button>
                                @endauth
                                @auth
                                    <a href="{{ route('nop-ho-so.show', ['maTTHC' => $tthc->maTTHC]) }}" class="btn btn-primary">
                                        <i class="fa-solid fa-paper-plane me-1"></i> Nộp hồ sơ trực tuyến
                                    </a>
                                @else
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <i class="fa-solid fa-paper-plane me-1"></i> Nộp hồ sơ trực tuyến
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <a class="btn btn-primary w-100" href="#modalmap">
                                <i class="fa-solid fa-map-location-dot me-1"></i> Xem bản đồ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Thông tin thủ tục hành chính-->

<!-- Modal xem bản đồ  -->
<div id="modalmap" class="modalmap" aria-modal="true" role="dialog" aria-labelledby="modalmapTitle">
    <div class="modalmap-dialog">
        <div class="modalmap-header bg-primary">
            <h2 id="modalmapTitle" class="modalmap-title">Bản đồ vị trí</h2>
            <a href="#" class="modalmap-close" aria-label="Đóng">&times;</a>
        </div>
        <div class="modalmap-body">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.858237982653!2d106.68427047508925!3d10.822158889329394!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3174deb3ef536f31%3A0x8b7bb8b7c956157b!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBDw7RuZyBuZ2hp4buHcCBUUC5IQ00!5e0!3m2!1svi!2s!4v1761728430059!5m2!1svi!2s" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>



</div>
<!-- Modal xem bản đồ  -->


<!--Modal xem chi tiết thủ tục hành chính-->
<div id="customModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header  bg-primary ">
            <h2 class="custom-modal-title">
                Thông tin chi tiết thủ tục
            </h2>
            <button type="button" class="custom-modal-close" onclick="closeCustomModal()">&times;</button>
        </div>
        <div class="custom-modal-body">
            <div class="row g-3 text-dark">
                <div class="row mb-3">
                    <strong class="col-2">Mã thủ tục:</strong>
                    <div class="col-10">{{ $tthc->maTTHC }}</div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Tên thủ tục:</strong>
                    <div class="col-10">{{ $tthc->tenTTHC }}</div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Lĩnh vực:</strong>
                    <div class="col-10">{{ $tthc->tenLinhVuc }}</div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Trình tự thực hiện:</strong>
                    <div class="col-10" style="text-align: justify;">{!! nl2br(e($tthc->trinhTuThucHien)) !!}</div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Cách thực hiện:</strong>
                    <div class="col-10">
                        <div class="table-responsive rounded">
                        <table class="table-service-cth table-sm   table-bordered align-middle mb-0">
                            <thead class="">
                                <tr>
                                    <th>Kênh</th>
                                    <th>Thời hạn giải quyết</th>
                                    <th>Mô tả phí/lệ phí</th>
                                    <th>Mô tả</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cachThucHiens as $cth)
                                    <tr>
                                        <td>{{ $cth->kenh }}</td>
                                        <td>{!! nl2br(e($cth->thoiHanGiaiQuyet)) !!}</td>
                                        <td>{!! nl2br(e($cth->moTaPhiLePhi)) !!}</td>
                                        <td>{!! nl2br(e($cth->moTa)) !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">—</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Thành phần hồ sơ:</strong>
                    <div class="col-10">
                        <div class="accordion" id="modalThanhPhanAccordion">
                            @forelse($thanhPhanHoSos as $tenThanhPhan => $giayTos)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="modalHeading{{ $loop->index }}">
                                        <button class="accordion-button bg-primary text-white {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#modalCollapse{{ $loop->index }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="modalCollapse{{ $loop->index }}">
                                            {{ $tenThanhPhan }}
                                            <span class="badge bg-dark ms-2">{{ $giayTos->count() }} giấy tờ</span>
                                        </button>
                                    </h2>
                                    <div id="modalCollapse{{ $loop->index }}"
                                         class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                         aria-labelledby="modalHeading{{ $loop->index }}"
                                         data-bs-parent="#modalThanhPhanAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive">
                                                <table class="table-service-cth table-sm text-dark table-bordered align-middle mb-0">
                                                    <thead class="">
                                                        <tr>
                                                            <th style="width: 60px">#</th>
                                                            <th>Tên giấy tờ</th>
                                                            <th style="width: 120px">Bản chính</th>
                                                            <th style="width: 120px">Bản sao</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($giayTos as $index => $tp)
                                                            @if($tp->tenGiayTo)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $tp->tenGiayTo }}</td>
                                                                    <td class="text-center">
                                                                        @if($tp->soLuongBanChinh)
                                                                            <span class="badge bg-success">{{ $tp->soLuongBanChinh }}</span>
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($tp->soLuongBanSao)
                                                                            <span class="badge bg-info">{{ $tp->soLuongBanSao }}</span>
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">Chưa có thông tin thành phần hồ sơ</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Đối tượng thực hiện:</strong>
                    <div class="col-10">
                        @forelse($doiTuongs as $dt)
                            <span class="badge bg-secondary me-1">{{ $dt->tenDoiTuong }}</span>
                        @empty
                            <div>—</div>
                        @endforelse
                    </div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Kết quả thực hiện:</strong>
                    <div class="col-10">{{ $tthc->ketQuaThucHien }}</div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Cơ quan thực hiện:</strong>
                    <div class="col-10">{{ $tthc->coQuanThucHien }}</div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Căn cứ pháp lý:</strong>
                    <div class="col-10">{!! nl2br(e($tthc->canCuPhapLy)) !!}</div>
                </div>
                <div class="row mb-3">
                    <strong class="col-2">Yêu cầu điều kiện:</strong>
                    <div class="col-10">{!! nl2br(e($tthc->yeuCauDieuKien)) !!}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Modal xem chi tiết thủ tục hành chính-->

<script>
function openCustomModal() {
    document.getElementById('customModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeCustomModal() {
    document.getElementById('customModal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('customModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCustomModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCustomModal();
    }
});

// Mở modal đăng nhập nếu có flag
@if(session('open_login_modal'))
    document.addEventListener('DOMContentLoaded', function() {
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    });
@endif
</script>
@endsection

