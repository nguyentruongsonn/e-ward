@extends('layouts.app')
@section('title', 'Danh sách dịch vụ công')
@section('content')
<div class="container-fluid page-header pt-5">
    <div class="container py-5">
        <form class="d-flex wow fadeInUp" data-wow-delay="0.3s" role="search" action="{{ route('services') }}" method="GET">
            <input class="form-control me-2" type="search" name="search" placeholder="Nhập từ khóa tìm kiếm" aria-label="Search" value="{{ request('search') }}">
            <button class="btn btn-color" type="submit">TÌM KIẾM</button>
        </form>
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">TRANG CHỦ</a></li>
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('services') }}">DANH SÁCH DỊCH VỤ</a></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-xxl py-5">
    <div class="container">
        <!-- Filter form -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" action="{{ route('services') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="maLinhVuc" class="form-label">Lọc theo lĩnh vực:</label>
                        <select name="maLinhVuc" id="maLinhVuc" class="form-select">
                            <option value="">Tất cả lĩnh vực</option>
                            @foreach($linhVucs as $lv)
                                <option value="{{ $lv->maLinhVuc }}" {{ request('maLinhVuc') == $lv->maLinhVuc ? 'selected' : '' }}>
                                    {{ $lv->tenLinhVuc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Tìm kiếm:</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Tên dịch vụ..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-color me-2">Tìm kiếm</button>
                        <a href="{{ route('services') }}" class="btn btn-secondary">Xóa bộ lọc</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Services list -->
        <div class="row">
            @forelse($tthcs as $tthc)
                <div class="col-lg-12 mb-3 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-items rounded h-100 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <a href="{{ route('outstanding-service.show', ['id' => $tthc->maTTHC]) }}" class="text-dark text-decoration-none">
                                    <i class="fa fa-file-lines me-3 icon-color" style="font-size: 25px;"></i>
                                    <strong>{{ $tthc->tenTTHC }}</strong>
                                </a>
                                @if($tthc->tenLinhVuc)
                                    <div class="mt-2">
                                        <span class="badge bg-secondary">{{ $tthc->tenLinhVuc }}</span>
                                    </div>
                                @endif
                            </div>
                            @auth
                                <div>
                                    <a href="{{ route('nop-ho-so.show', $tthc->maTTHC) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-paper-plane me-1"></i> Nộp hồ sơ
                                    </a>
                                </div>
                            @else
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <i class="fa fa-paper-plane me-1"></i> Nộp hồ sơ
                                    </button>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle me-2"></i>
                        Không tìm thấy dịch vụ nào. Vui lòng thử lại với từ khóa khác.
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $tthcs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

