@extends('layouts.app')


@section('content')
<div class="container-fluid page-header py-5 mb-5">
    <div class="container py-5">
        <form class="d-flex wow fadeInUp" data-wow-delay="0.3" role="search">
            <input class="form-control me-2" type="search" placeholder="Nhập từ khóa tìm kiếm" aria-label="Search">
            <button class="btn btn-primary" type="submit">TÌM KIẾM</button>
        </form>
    </div>

    <div class="container py-4">
        <div class="row g-4 overflow-hidden ">
            <div class="col-lg-4 service-item rounded wow fadeInUp" data-wow-delay="0.1" >
                <a href="#" class="rounded py-3">DỊCH VỤ TRỰC TUYẾN</a>
            </div>
            <div class="col-lg-4 service-item rounded wow fadeInUp" data-wow-delay="0.3" ">
                <a href="#" class="rounded py-3">DỊCH VỤ NỔI BẬT</a>
            </div>
            <div class="col-lg-4 service-item rounded wow fadeInUp" data-wow-delay="0.5">
                <a href="#" class="rounded py-3">ĐẶT LỊCH NỘP HỒ SƠ</a>
            </div>
        </div>
    </div>
</div>

<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="object pb-2">
                    <a href="#" class="d-flex text-dark">CÔNG DÂN</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"> <i class="fa-solid fa-id-card"></i> Hộ tịch</a>
                </div>

                <div class="object-items rounded ">
                    <a href="#" class="d-flex"> <i class="fa-solid fa-file-signature"></i> Chứng thực</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"> <i class="fa-solid fa-house-user"></i> Đăng ký cư trú</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-map"></i> Đất đai</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-medal"></i> Người có công</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-hand-holding-heart"></i> Bảo trợ xã hội</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"> <i class="fa-solid fa-children"></i> Nuôi con nuôi</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"> <i class="fa-solid fa-fire-extinguisher"></i> Phòng cháy chữa cháy</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-utensils"></i> An toàn toàn thưc phẩm</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-briefcase"></i> Lao động và việc làm</a>
                </div>


            </div>

            <div class="col-lg-6 col-lg-6 wow fadeInUp" data-wow-delay="0.3s ">
                <div class="object pb-2">
                    <a href="#" class="d-flex text-dark">DOANH NGHIỆP</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-store"></i> Thành lập và hoạt động hộ kinh doanh</a>
                </div>

                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-apple-whole"></i> An toàn thực phẩm</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-building"></i> Xây dựng</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-leaf"></i> Môi trường</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-paw"></i> Thú y / Chăn nuôi / Trồng trọt</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"> <i class="fa-solid fa-handshake"></i> Kinh tế hợp tác – Hợp tác xã</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"> <i class="fa-solid fa-fire"></i> Phòng cháy, chữa cháy</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"> <i class="fa-solid fa-users"></i> Lao động, tiền lương</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"> <i class="fa-solid fa-house"></i> Nhà ở và công sở</a>
                </div>
                <div class="object-items rounded ">
                    <a href="#" class="d-flex"><i class="fa-solid fa-water"></i>Tài nguyên nước / Thủy lợi</a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
