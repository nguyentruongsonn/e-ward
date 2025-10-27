@extends('layouts.app')
@section('title', 'Dịch vụ công nổi bật')
@section('content')
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

<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4">
            @foreach($tthcs as $tthc)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item rounded h-100 p-4">
                        <h5 class="mb-3">{{ $tthc->tenTTHC }}</h5>
                        <p>Thời hạn: {{ $tthc->thoiHanGiaiQuyet }}</p>
                        <p>Phí: {{ number_format($tthc->phi, 0, ',', '.') }} đ</p>
                        <a href="#" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
