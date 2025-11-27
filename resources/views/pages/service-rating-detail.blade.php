@extends('layouts.app')

@section('content')
    <style>
        .progress {
            height: 8px;
            border-radius: 4px;
        }
        .rating-summary-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .comment-card {
            border-bottom: 1px solid #eee;
            padding: 1.5rem 0;
        }
        .comment-card:last-child {
            border-bottom: none;
        }
        .avatar-placeholder {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-weight: bold;
        }
    </style>

    <div class="container-fluid page-header py-5 mb-5">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Chi tiết đánh giá</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mobile-font">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('service.ratings') }}">Đánh giá dịch vụ</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Left Column: Procedure Info & Stats -->
                <div class="col-lg-4">
                    <div class="rating-summary-card p-4 mb-4 wow fadeInUp" data-wow-delay="0.1s">
                        <h5 class="mb-3 text-primary">{{ $procedure->tenTTHC }}</h5>
                        <div class="text-center mb-4">
                            <h1 class="display-4 fw-bold mb-0">{{ number_format($stats->avg_score, 1) }}</h1>
                            <div class="text-info mb-2">
                                @php $score = round($stats->avg_score); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-star {{ $i <= $score ? '' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <p class="text-muted">{{ $stats->total_ratings }} lượt đánh giá</p>
                        </div>

                        <!-- Rating Distribution -->
                        <div class="rating-bars">
                            @foreach([5, 4, 3, 2, 1] as $star)
                                @php
                                    $countVar = match($star) {
                                        5 => 'five_star',
                                        4 => 'four_star',
                                        3 => 'three_star',
                                        2 => 'two_star',
                                        1 => 'one_star',
                                    };
                                    $count = $stats->$countVar;
                                    $percent = $stats->total_ratings > 0 ? ($count / $stats->total_ratings) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted me-2" style="width: 20px;">{{ $star }}</span>
                                    <i class="fa fa-star text-info me-2" style="font-size: 0.8rem;"></i>
                                    <div class="progress flex-grow-1 bg-light">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: {{ $percent }}%" 
                                             aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="text-muted ms-2 small" style="width: 30px; text-align: right;">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column: Comments List -->
                <div class="col-lg-8">
                    <div class="wow fadeInUp" data-wow-delay="0.3s">
                        <h4 class="mb-4">Nhận xét từ công dân</h4>
                        
                        @if($ratings->count() > 0)
                            <div class="bg-white p-4 rounded shadow-sm">
                                @foreach($ratings as $rating)
                                    <div class="comment-card">
                                        <div class="d-flex mb-3">
                                            <div class="avatar-placeholder me-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center mb-1">
                                                    <h6 class="mb-0 me-2">Công dân</h6>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($rating->ngayDanhGia)->format('d/m/Y H:i') }}
                                                    </small>
                                                </div>
                                                <div class="text-info" style="font-size: 0.8rem;">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fa fa-star {{ $i <= $rating->soDiem ? '' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-dark mb-0 ps-5 ms-2">{{ $rating->nhanXet }}</p>
                                    </div>
                                @endforeach

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $ratings->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5 bg-white rounded shadow-sm">
                                <i class="fas fa-comment-slash fa-3x text-muted mb-3 opacity-25"></i>
                                <p class="text-muted mb-0">Chưa có nhận xét nào cho thủ tục này.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
