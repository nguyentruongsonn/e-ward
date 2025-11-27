@extends('layouts.app')

@section('content')
    <style>
        .rating-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .rating-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
        .star-rating .fa-star {
            color: #ffc107;
        }
        .star-rating .fa-star.text-muted {
            color: #e4e5e9;
        }
    </style>

    <div class="container-fluid page-header py-5 mb-5">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Đánh giá chất lượng dịch vụ</h1>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h6 class="section-title bg-white text-center text-primary px-3">Đánh giá từ công dân</h6>
                <h1 class="display-6 mb-4">Đánh giá chất lượng dịch vụ</h1>
            </div>

            <div class="row g-4">
                <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th class="text-center py-3" style="width: 60px;">STT</th>
                                            <th class="py-3">Tên thủ tục hành chính</th>
                                            <th class="text-center py-3" style="width: 250px;">Điểm đánh giá</th>
                                            <th class="text-center py-3" style="width: 150px;">Lượt đánh giá</th>
                                            <th class="text-center py-3" style="width: 100px;">Chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($ratings) && $ratings->count() > 0)
                                            @foreach($ratings as $index => $rating)
                                                <tr class="align-middle rating-row" 
                                                    onclick="window.location.href='{{ route('service.ratings.detail', $rating->maTTHC) }}'"
                                                    style="cursor: pointer;">
                                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                                    <td class="fw-bold">{{ $rating->tenTTHC }}</td>
                                                    <td class="text-center">
                                                        <div class="star-rating mb-1">
                                                            @php $score = round($rating->avg_score); @endphp
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fa fa-star {{ $i <= $score ? '' : 'text-muted' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <span class="badge bg-light text-dark border">
                                                            {{ number_format($rating->avg_score, 1) }}/5
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info text-white rounded-pill px-3">
                                                            {{ $rating->total_ratings }} lượt
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('service.ratings.detail', $rating->maTTHC) }}" class="btn btn-sm btn-outline-primary rounded-circle">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                                    <p class="mb-0">Chưa có dữ liệu đánh giá nào.</p>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
