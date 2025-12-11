@extends('layouts.app')

@section('title', 'Các câu hỏi thường gặp')

@section('content')
    @include('partials.support-header', ['breadcrumb' => 'Các câu hỏi thường gặp'])

    <div class="container py-5">
        <div class="row g-4">
            <aside class="col-lg-3 support-sidebar">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Danh mục hỗ trợ</div>
                    <div class="list-group list-group-flush">
                        <a href="{{ url('/support/guide') }}" class="list-group-item list-group-item-action">Hướng dẫn
                            chung</a>
                        <a href="{{ url('/support/faq') }}" class="list-group-item list-group-item-action active">Các câu hỏi
                            thường gặp</a>
                    </div>
                </div>
            </aside>

            <main class="col-lg-9 support-faq-page">
                <h1 class="main-title mb-4" style="color: #007bff; font-weight: bold;">
                    <i class="fa fa-question-circle"></i> Các câu hỏi thường gặp
                </h1>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('support.faq') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-8">
                                    <label for="faqSearch" class="form-label fw-semibold">
                                        <i class="fa fa-search"></i> Tìm kiếm câu hỏi
                                    </label>
                                    <input id="faqSearch" name="q" value="{{ $q ?? '' }}" type="text" 
                                           class="form-control form-control-lg"
                                           placeholder="Nhập từ khóa bạn muốn tìm..." />
                                </div>
                                <div class="col-md-4">
                                    <label for="faqCategory" class="form-label fw-semibold">
                                        <i class="fa fa-filter"></i> Lọc theo dịch vụ
                                    </label>
                                    <select id="faqCategory" name="category" class="form-select form-select-lg">
                                        <option value="">Tất cả dịch vụ</option>
                                        @foreach ($categories ?? [] as $cat)
                                            <option value="{{ $cat->id_loaicauhoi }}"
                                                {{ isset($categoryId) && (string) $categoryId === (string) $cat->id_loaicauhoi ? 'selected' : '' }}>
                                                {{ $cat->name_loaicauhoi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3 d-flex gap-2">
                                <button class="btn btn-primary btn-lg px-4" type="submit">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('support.faq') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fa fa-refresh"></i> Đặt lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                @php $hasResults = isset($faqs) && $faqs->count() > 0; @endphp
                
                @if($hasResults)
                    <div class="alert alert-info mb-3">
                        <i class="fa fa-info-circle"></i> 
                        Tìm thấy <strong>{{ $faqs->count() }}</strong> câu hỏi
                        @if($q) liên quan đến "<strong>{{ $q }}</strong>"@endif
                        @if($categoryId)
                            @php $selectedCategory = $categories->firstWhere('id_loaicauhoi', $categoryId); @endphp
                            trong <strong>{{ $selectedCategory->name_loaicauhoi ?? 'dịch vụ đã chọn' }}</strong>
                        @endif
                    </div>
                @endif

                <div id="faqEmpty" class="card shadow-sm {{ $hasResults ? 'd-none' : '' }}">
                    <div class="card-body text-center py-5">
                        <i class="fa fa-search text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h4 class="mt-3 text-muted">Không tìm thấy kết quả</h4>
                        <p class="text-muted">Vui lòng thử tìm kiếm với từ khóa khác hoặc chọn dịch vụ từ danh sách</p>
                    </div>
                </div>

                <div id="faqAccordion" class="accordion faq-accordion {{ $hasResults ? '' : 'd-none' }}">
                    @if ($hasResults)
                        @foreach ($faqs as $index => $item)
                            @php
                                $collapseId = 'faqc' . $index;
                                $headingId = 'faqh' . $index;
                                $category = $categories->firstWhere('id_loaicauhoi', $item->id_loaicauhoi);
                            @endphp
                            <div class="accordion-item border rounded mb-2 shadow-sm">
                                <h2 class="accordion-header" id="{{ $headingId }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#{{ $collapseId }}" aria-expanded="false"
                                        aria-controls="{{ $collapseId }}" style="background: #f8f9fa;">
                                        <div class="w-100">
                                            <i class="fa fa-question-circle text-primary me-2"></i>
                                            <strong>{{ $item->cauhoi }}</strong>
                                            @if($category)
                                                <span class="badge bg-info ms-2" style="font-size: 0.75rem;">
                                                    {{ $category->name_loaicauhoi }}
                                                </span>
                                            @endif
                                        </div>
                                    </button>
                                </h2>
                                <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                    aria-labelledby="{{ $headingId }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body" style="background: white; padding: 1.5rem;">
                                        <i class="fa fa-comment-o text-success me-2"></i>
                                        <strong>Trả lời:</strong>
                                        <div class="mt-2" style="line-height: 1.8;">
                                            {!! nl2br(e($item->dapan)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="card shadow-sm mt-4 border-primary" style="border-left-width: 4px;">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="fa fa-question-circle"></i> Không tìm thấy câu trả lời phù hợp?
                        </h5>
                        <p class="card-text text-muted mb-0">
                            Hãy liên hệ với bộ phận hỗ trợ của chúng tôi để được giải đáp thắc mắc. 
                            Chúng tôi luôn sẵn sàng hỗ trợ bạn!
                        </p>
                    </div>
                </div>

                <!-- Kết quả hiển thị dựa trên server-side filter -->
            </main>
        </div>
    </div>
@endsection
