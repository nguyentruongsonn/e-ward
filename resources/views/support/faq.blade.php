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
                <h1 class="main-title -none">Các câu hỏi thường gặp</h1>

                <form method="GET" action="{{ route('support.faq') }}" class="mb-3">
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-md-9">
                            <input name="q" value="{{ $q ?? '' }}" type="text" class="form-control"
                                placeholder="Nhập câu hỏi tìm kiếm" />
                        </div>
                        <div class="col-md-3 d-grid d-md-block">
                            <button class="btn btn-faq-search w-100" type="submit">Tìm kiếm</button>
                        </div>
                    </div>

                    <div>
                        <label for="faqCategory" class="form-label small mb-1">Chọn dịch vụ</label>
                        <select id="faqCategory" name="category" class="form-select" onchange="this.form.submit()">
                            <option value="">Chọn dịch vụ</option>
                            @foreach ($categories ?? [] as $cat)
                                <option value="{{ $cat->id_loaicauhoi }}"
                                    {{ isset($categoryId) && (string) $categoryId === (string) $cat->id_loaicauhoi ? 'selected' : '' }}>
                                    {{ $cat->name_loaicauhoi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                @php $hasResults = isset($faqs) && $faqs->count() > 0; @endphp
                <div id="faqEmpty" class="py-4 text-center text-muted {{ $hasResults ? 'd-none' : '' }}">Không tìm thấy
                    thông tin tìm kiếm</div>

                <div id="faqAccordion" class="accordion faq-accordion {{ $hasResults ? '' : 'd-none' }}">
                    @if ($hasResults)
                        @foreach ($faqs as $index => $item)
                            @php
                                $collapseId = 'faqc' . $index;
                                $headingId = 'faqh' . $index;
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="{{ $headingId }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#{{ $collapseId }}" aria-expanded="false"
                                        aria-controls="{{ $collapseId }}">
                                        {{ $item->cauhoi }}
                                    </button>
                                </h2>
                                <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                    aria-labelledby="{{ $headingId }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">{!! nl2br(e($item->dapan)) !!}</div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <p class="mt-4 text-muted">Không thấy câu trả lời phù hợp? Hãy liên hệ bộ phận hỗ trợ.</p>

                <!-- Kết quả hiển thị dựa trên server-side filter -->
            </main>
        </div>
    </div>
@endsection
