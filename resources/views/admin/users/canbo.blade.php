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

                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-sm-12 text-right">
                                @if(auth()->user() && trim(auth()->user()->vaiTro) === 'Quản trị viên')
                                    <a href="{{ url('admin/users/canbo/create') }}" class="btn btn-success">
                                        <i class="fa fa-plus"></i> Thêm cán bộ
                                    </a>
                                @endif
                            </div>
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
                                        <th>Hành động</th>
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
                                        <td>
                                            @if(!empty($canBo->IDCB))
                                                <a href="{{ route('admin.users.canbo.show', $canBo->IDCB) }}"
                                                   class="btn btn-xs btn-info"
                                                   title="Xem cán bộ">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if(auth()->user() && trim(auth()->user()->vaiTro) === 'Quản trị viên')
                                                    <a href="{{ route('admin.users.canbo.edit', $canBo->IDCB) }}"
                                                       class="btn btn-xs btn-primary"
                                                       title="Sửa cán bộ">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.users.canbo.destroy', $canBo->IDCB) }}"
                                                          method="POST"
                                                          style="display:inline-block;"
                                                          onsubmit="return confirm('Bạn chắc chắn muốn xóa cán bộ này?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-danger" title="Xóa cán bộ">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
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

