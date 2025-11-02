
<div class="support-header">
    <div class="container d-flex">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('support.about') ? 'active' : '' }}"
                   href="{{ route('support.about') }}">Giới thiệu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('support.terms') ? 'active' : '' }}"
                   href="{{ route('support.terms') }}">Điều khoản sử dụng</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('support.guide') ? 'active' : '' }}"
                   href="{{ route('support.guide') }}">Hướng dẫn sử dụng</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('support.notice') ? 'active' : '' }}"
                   href="{{ route('support.notice') }}">Thông báo</a>
            </li>
        </ul>
    </div>
</div>

<div class="container">
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb ?? '' }}</li>
        </ol>
    </nav>
</div>
