@extends('layouts.app')

@section('title', 'Thông báo')

@section('content')
    @include('partials.support-header', ['breadcrumb' => 'Thông báo'])

    <div class="container py-5 support-notice-page">
        <h1 class="main-title -none">Thông báo</h1>

        @php $hasNotices = isset($notices) && $notices->count() > 0; @endphp
        <div class="text-muted {{ $hasNotices ? 'd-none' : '' }}">Hiện chưa có thông báo.</div>

        <div class="row g-4 {{ $hasNotices ? '' : 'd-none' }}">
            @foreach ($notices as $notice)
                <div class="col-md-6 col-lg-4">
                    <div class="h-100 p-3 border rounded-3 notice-card">
                        <h5 class="mb-2">
                            <a href="#" class="text-decoration-none notice-open"
                               data-bs-toggle="modal"
                               data-bs-target="#noticeModal"
                               data-title="{{ e($notice->title) }}"
                               data-id="{{ $notice->id }}">
                                {{ $notice->title }}
                            </a>
                        </h5>
                        <div class="small text-muted mb-2">{{ optional($notice->create_at)->format('d/m/Y H:i') }}</div>
                        <div class="text-truncate" style="-webkit-line-clamp: 3; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;">
                            {!! nl2br(e(\Illuminate\Support\Str::limit($notice->content, 200))) !!}
                        </div>

                        <div class="d-none notice-content">
                            {!! nl2br(e($notice->content)) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($hasNotices)
            <div class="mt-4">
                {{ $notices->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade notice-modal" id="noticeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noticeModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="noticeModalBody"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .support-notice-page .border {
                border-color: #CE7A58 !important;
            }
            .support-notice-page a.notice-open:hover {
                color: #CE7A58;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function() {
                const modal = document.getElementById('noticeModal');
                if (!modal) return;

                document.addEventListener('click', function(e) {
                    const link = e.target.closest('.notice-open');
                    if (!link) return;
                    const card = link.closest('.col-md-6, .col-lg-4, .h-100');
                    const container = link.closest('.h-100');
                    const hidden = container ? container.querySelector('.notice-content') : null;
                    const title = link.getAttribute('data-title') || '';
                    const bodyEl = document.getElementById('noticeModalBody');
                    const titleEl = document.getElementById('noticeModalTitle');
                    if (titleEl) titleEl.textContent = title;
                    if (bodyEl) bodyEl.innerHTML = hidden ? hidden.innerHTML : '';
                });
            })();
        </script>
    @endpush
@endsection
