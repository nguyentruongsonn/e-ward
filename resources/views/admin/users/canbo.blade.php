@extends('admin.layout')

@section('title', 'Quản lý Cán bộ')

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-user-tie"></i> Quản lý Cán bộ</h3>
                    </header>
                    <div class="panel-body">
                        <!-- Filter form -->
                        <div class="row" style="margin-bottom: 20px;">
                            <form method="GET" action="{{ route('admin.users.canbo') }}" class="form-inline">
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <label for="search" style="margin-right: 5px; font-weight: normal;">Tìm kiếm:</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Họ tên, email, SĐT, CCCD..." 
                                           value="{{ request('search') }}" style="width: 300px;">
                                </div>
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <label for="vaiTro" style="margin-right: 5px; font-weight: normal;">Vai trò:</label>
                                    <select name="vaiTro" id="vaiTro" class="form-control" style="width: 180px;">
                                        <option value="">Tất cả vai trò</option>
                                        <option value="Cán bộ một cửa" {{ request('vaiTro') == 'Cán bộ một cửa' ? 'selected' : '' }}>Cán bộ một cửa</option>
                                        <option value="Cán bộ thụ lý" {{ request('vaiTro') == 'Cán bộ thụ lý' ? 'selected' : '' }}>Cán bộ thụ lý</option>
                                        <option value="Lãnh đạo" {{ request('vaiTro') == 'Lãnh đạo' ? 'selected' : '' }}>Lãnh đạo</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-right: 10px; margin-bottom: 10px;">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('admin.users.canbo') }}" class="btn btn-default" style="margin-bottom: 10px;">
                                    <i class="fa fa-refresh"></i> Xóa bộ lọc
                                </a>
                            </form>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Mã CCCD</th>
                                        <th>Vai trò</th>
                                        <th>Quầy làm việc</th>
                                        <th>Mã cán bộ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($canBos as $canBo)
                                    <tr>
                                        <td>{{ $canBo->IDnguoiDung }}</td>
                                        <td>{{ $canBo->hoTen ?? '-' }}</td>
                                        <td>{{ $canBo->email ?? '-' }}</td>
                                        <td>{{ $canBo->soDienThoai ?? '-' }}</td>
                                        <td>{{ $canBo->maCCCD ?? '-' }}</td>
                                        <td>
                                            @if($canBo->vaiTro == 'Cán bộ một cửa')
                                                <span class="badge bg-primary">{{ $canBo->vaiTro }}</span>
                                            @elseif($canBo->vaiTro == 'Cán bộ thụ lý')
                                                <span class="badge bg-success">{{ $canBo->vaiTro }}</span>
                                            @elseif($canBo->vaiTro == 'Lãnh đạo')
                                                <span class="badge bg-warning">{{ $canBo->vaiTro }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $canBo->vaiTro ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $canBo->tenQuayLamViec ?? 'Chưa phân quầy' }}</td>
                                        <td>{{ $canBo->IDCB ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="text-center">
                            {{ $canBos->links() }}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

