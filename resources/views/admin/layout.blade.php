<!DOCTYPE html>
<head>
<title>@yield('title', 'Admin Panel') - E-Ward</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Admin Panel, E-Ward" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- bootstrap-css -->
<link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}" >
<!-- //bootstrap-css -->
<!-- Custom CSS -->
<link href="{{ asset('admin/css/style.css') }}" rel='stylesheet' type='text/css' />
<link href="{{ asset('admin/css/style-responsive.css') }}" rel="stylesheet"/>
<!-- font CSS -->
<link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<!-- font-awesome icons -->
<link rel="stylesheet" href="{{ asset('admin/css/font.css') }}" type="text/css"/>
<link href="{{ asset('admin/css/font-awesome.css') }}" rel="stylesheet">
<!-- //font-awesome icons -->
<script src="{{ asset('admin/js/jquery2.0.3.min.js') }}"></script>
@stack('styles')
</head>
<body>
<section id="container">
<!--header start-->
<header class="header fixed-top clearfix">
<!--logo start-->
<div class="brand">
    <a href="{{ route('admin.dashboard') }}" class="logo">
        ADMIN
    </a>
    <div class="sidebar-toggle-box">
        <div class="fa fa-bars"></div>
    </div>
</div>
<!--logo end-->
<div class="nav notify-row" id="top_menu">
    <!--  notification start -->
    <ul class="nav top-menu">
        <!-- notification dropdown start-->
        <li id="header_notification_bar" class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <i class="fa fa-bell-o"></i>
                <span class="badge bg-warning">0</span>
            </a>
            <ul class="dropdown-menu extended notification">
                <li>
                    <p>Thông báo</p>
                </li>
                <li>
                    <div class="alert alert-info clearfix">
                        <span class="alert-icon"><i class="fa fa-info"></i></span>
                        <div class="noti-info">
                            <a href="#"> Chưa có thông báo mới</a>
                        </div>
                    </div>
                </li>
            </ul>
        </li>
        <!-- notification dropdown end -->
    </ul>
    <!--  notification end -->
</div>
<div class="top-nav clearfix">
    <!--search & user info start-->
    <ul class="nav pull-right top-menu">
        <li>
            <input type="text" class="form-control search" placeholder=" Tìm kiếm">
        </li>
        <!-- user login dropdown start-->
        <li class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <img alt="" src="{{ asset('admin/images/2.png') }}">
                <span class="username">{{ Auth::user()->hoTen ?? 'Admin' }}</span>
                <b class="caret"></b>
            </a>
            <ul class="dropdown-menu extended logout">
                <li><a href="#"><i class=" fa fa-suitcase"></i> Hồ sơ</a></li>
                <li><a href="#"><i class="fa fa-cog"></i> Cài đặt</a></li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; width: 100%; text-align: left; padding: 10px 20px;">
                            <i class="fa fa-key"></i> Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </li>
        <!-- user login dropdown end -->
    </ul>
    <!--search & user info end-->
</div>
</header>
<!--header end-->
<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @php
                    $user = Auth::user();
                @endphp

                {{-- Quản lý hồ sơ - All staff roles can see --}}
                <li class="sub-menu">
                    <a href="javascript:;" class="{{ request()->routeIs('admin.hosoxuly.*') ? 'active' : '' }}">
                        <i class="fa fa-file-text"></i>
                        <span>Quản lý hồ sơ</span>
                    </a>
                    <ul class="sub">
                        @if($user->vaiTro === 'Cán bộ một cửa' || $user->vaiTro === 'Quản trị viên')
                            <li><a href="{{ route('admin.hosoxuly.index') }}" class="{{ request()->routeIs('admin.hosoxuly.index') ? 'active' : '' }}">Hồ sơ chờ tiếp nhận</a></li>
                            <li><a href="{{ route('admin.hosoxuly.nhan-truc-tiep') }}" class="{{ request()->routeIs('admin.hosoxuly.nhan-truc-tiep') ? 'active' : '' }}">Hồ sơ nhận trực tiếp</a></li>
                        @endif
                        @if($user->vaiTro === 'Cán bộ thụ lý' || $user->vaiTro === 'Quản trị viên')
                            <li><a href="{{ route('admin.hosoxuly.tiepnhan') }}" class="{{ request()->routeIs('admin.hosoxuly.tiepnhan') ? 'active' : '' }}">Hồ sơ đã tiếp nhận</a></li>
                        @endif
                        @if($user->vaiTro === 'Lãnh đạo' || $user->vaiTro === 'Quản trị viên')
                            <li><a href="{{ route('admin.hosoxuly.cho-xuly') }}" class="{{ request()->routeIs('admin.hosoxuly.cho-xuly') ? 'active' : '' }}">Hồ sơ chờ phê duyệt</a></li>
                        @endif
                        <li><a href="{{ route('admin.hosoxuly.danh-sach-yeu-cau-bo-sung') }}" class="{{ request()->routeIs('admin.hosoxuly.danh-sach-yeu-cau-bo-sung') ? 'active' : '' }}">Hồ sơ yêu cầu bổ sung</a></li>
                        <li><a href="{{ route('admin.hosoxuly.da-xu-ly-xong') }}" class="{{ request()->routeIs('admin.hosoxuly.da-xu-ly-xong') ? 'active' : '' }}">Hồ sơ đã xử lý xong</a></li>
                        <li><a href="{{ route('admin.hosoxuly.da-tra-ket-qua') }}" class="{{ request()->routeIs('admin.hosoxuly.da-tra-ket-qua') ? 'active' : '' }}">Hồ sơ đã trả kết quả</a></li>
                        <li><a href="{{ route('admin.hosoxuly.all') }}" class="{{ request()->routeIs('admin.hosoxuly.all') ? 'active' : '' }}">Danh sách tất cả hồ sơ</a></li>
                    </ul>
                </li>

                {{-- Lịch hẹn - All staff roles can see --}}
                @if($user->vaiTro === 'Cán bộ một cửa' || $user->vaiTro === 'Cán bộ thụ lý' || $user->vaiTro === 'Lãnh đạo' || $user->vaiTro === 'Quản trị viên')
                <li class="sub-menu">
                    <a href="javascript:;" class="{{ request()->routeIs('admin.appointment.*') ? 'active' : '' }}">
                        <i class="fa fa-calendar"></i>
                        <span>Lịch hẹn</span>
                    </a>
                    <ul class="sub">
                        {{-- Quét QR Code - Only for Quản trị viên --}}
                        @if($user->vaiTro === 'Quản trị viên')
                        <li><a href="{{ route('admin.appointment.scan') }}" class="{{ request()->routeIs('admin.appointment.scan') ? 'active' : '' }}">Quét QR Code</a></li>
                        @endif
                        <li><a href="{{ route('admin.appointment.index') }}" class="{{ request()->routeIs('admin.appointment.index') ? 'active' : '' }}">Danh sách lịch hẹn</a></li>
                        <li><a href="{{ route('admin.appointment.today') }}" class="{{ request()->routeIs('admin.appointment.today') ? 'active' : '' }}">Lịch hẹn hôm nay</a></li>
                    </ul>
                </li>
                @endif

                {{-- Quản lý người dùng - Cho phép tất cả Cán bộ --}}
                @if(in_array($user->vaiTro, ['Cán bộ một cửa', 'Cán bộ thụ lý', 'Lãnh đạo', 'Quản trị viên']))
                    <li class="sub-menu">
                        <a href="javascript:;" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fa fa-users"></i>
                            <span>Quản lý người dùng</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{ route('admin.users.congdan') }}" class="{{ request()->routeIs('admin.users.congdan') ? 'active' : '' }}">Công dân</a></li>
                            <li><a href="{{ route('admin.users.canbo') }}" class="{{ request()->routeIs('admin.users.canbo') ? 'active' : '' }}">Cán bộ</a></li>
                        </ul>
                    </li>
                @endif

                {{-- Thủ tục hành chính - Only Admin --}}
                @if($user->vaiTro === 'Quản trị viên')
                    <li class="sub-menu">
                        <a href="javascript:;" class="{{ request()->routeIs('admin.tthc.*') ? 'active' : '' }}">
                            <i class="fa fa-book"></i>
                            <span>Thủ tục hành chính</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{ route('admin.tthc.index') }}" class="{{ request()->routeIs('admin.tthc.index') ? 'active' : '' }}">Danh sách TTHC</a></li>
                            <li><a href="{{ route('admin.tthc.create') }}" class="{{ request()->routeIs('admin.tthc.create') ? 'active' : '' }}">Thêm TTHC mới</a></li>
                            <li><a href="{{ route('admin.tthc.linhvuc.index') }}" class="{{ request()->routeIs('admin.tthc.linhvuc.*') ? 'active' : '' }}">Lĩnh vực</a></li>
                        </ul>
                    </li>

                    {{-- Thanh toán - Only Admin --}}
                    <li class="sub-menu">
                        <a href="javascript:;" class="{{ request()->routeIs('admin.payment.*') ? 'active' : '' }}">
                            <i class="fa fa-money"></i>
                            <span>Thanh toán</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{ route('admin.payment.history') }}" class="{{ request()->routeIs('admin.payment.history') ? 'active' : '' }}">Lịch sử thanh toán</a></li>
                            <li><a href="{{ route('admin.payment.revenue') }}" class="{{ request()->routeIs('admin.payment.revenue') ? 'active' : '' }}">Báo cáo doanh thu</a></li>
                        </ul>
                    </li>
                @endif

                <li>
                    <a href="{{ route('home') }}" target="_blank">
                        <i class="fa fa-globe"></i>
                        <span>Về trang chủ</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->

@yield('content')

</section>
<!--main content end-->
<script src="{{ asset('admin/js/bootstrap.js') }}"></script>
<script src="{{ asset('admin/js/jquery.dcjqaccordion.2.7.js') }}"></script>
<script src="{{ asset('admin/js/scripts.js') }}"></script>
<script src="{{ asset('admin/js/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('admin/js/jquery.nicescroll.js') }}"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="{{ asset('admin/js/flot-chart/excanvas.min.js') }}"></script><![endif]-->
<script src="{{ asset('admin/js/jquery.scrollTo.js') }}"></script>
<script>
	$(document).ready(function() {
		//BOX BUTTON SHOW AND CLOSE
	   jQuery('.small-graph-box').hover(function() {
		  jQuery(this).find('.box-button').fadeIn('fast');
	   }, function() {
		  jQuery(this).find('.box-button').fadeOut('fast');
	   });
	   jQuery('.small-graph-box .box-close').click(function() {
		  jQuery(this).closest('.small-graph-box').fadeOut(200);
		  return false;
	   });
	});
</script>
@stack('scripts')
</body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</html>

