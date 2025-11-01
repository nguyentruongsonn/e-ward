
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
        @php
            // Nhóm thủ tục theo tên đối tượng thực hiện
            $groups = [];
            foreach ($tthcs as $t) {
                $list = $t->doiTuongs ?? collect();
                if ($list->isEmpty()) {
                    $groups['Tất cả'][] = $t;
                } else {
                    foreach ($list as $d) {
                        $name = $d->tenDoiTuong ?? 'Khác';
                        $groups[$name][] = $t;
                    }
                }
            }
            // Sắp xếp key ưu tiên
            $orderedKeys = array_keys($groups);
            usort($orderedKeys, function($a, $b) {
                $order = ['Công dân' => 1, 'Doanh nghiệp' => 2];
                return ($order[$a] ?? 99) <=> ($order[$b] ?? 99);
            });
        @endphp

        <div class="instance mb-4">
            <ul class="nav nav-tabs border-0" role="tablist">
                @foreach($orderedKeys as $idx => $key)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border me-2 {{ $idx==0 ? 'active' : '' }}" id="tab-{{ Illuminate\Support\Str::slug($key) }}" data-bs-toggle="tab" data-bs-target="#panel-{{ Illuminate\Support\Str::slug($key) }}" type="button" role="tab" aria-controls="panel-{{ Illuminate\Support\Str::slug($key) }}" aria-selected="{{ $idx==0 ? 'true' : 'false' }}">{{ $key }}</button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content">
            @foreach($orderedKeys as $idx => $key)
                <div class="tab-pane fade {{ $idx==0 ? 'show active' : '' }}" id="panel-{{ Illuminate\Support\Str::slug($key) }}" role="tabpanel" aria-labelledby="tab-{{ Illuminate\Support\Str::slug($key) }}">
                    <div class="row">
                        @foreach($groups[$key] as $tthc)
                            <div class="col-lg-12  wow fadeInUp" data-wow-delay="0.1s">
                                <div class="service-items rounded h-100 p-4">
                                    <a href="{{ route('outstanding-service.show', ['id' => $tthc->maTTHC]) }}" class="text-dark">
                                        <i class="fa fa-file-lines me-3" style="color: green; font-size: 25px;"></i>
                                        {{ $tthc->tenTTHC }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
