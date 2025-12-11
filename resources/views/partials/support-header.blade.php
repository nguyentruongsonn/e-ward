{{-- Support Page Header with Breadcrumb --}}
<div class="bg-light py-4 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('support.guide') }}">Hỗ trợ</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb ?? 'Hỗ trợ' }}</li>
            </ol>
        </nav>
        @if(isset($breadcrumb))
            <h2 class="mt-3 mb-0" style="color: #007bff; font-weight: bold;">
                <i class="fa fa-life-ring"></i> {{ $breadcrumb }}
            </h2>
        @endif
    </div>
</div>
