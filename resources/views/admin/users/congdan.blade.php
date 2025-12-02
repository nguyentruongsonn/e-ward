@extends('admin.layout')

@section('title', 'Quản lý Công dân')

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-users"></i> Quản lý Công dân</h3>
                    </header>
                    <div class="panel-body">
                        <!-- Filter form -->
                        <div class="row" style="margin-bottom: 20px;">
                            <form method="GET" action="{{ route('admin.users.congdan') }}" class="form-inline">
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <label for="search" style="margin-right: 5px; font-weight: normal;">Tìm kiếm:</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Họ tên, email, SĐT, CCCD..." 
                                           value="{{ request('search') }}" style="width: 300px;">
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-right: 10px; margin-bottom: 10px;">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('admin.users.congdan') }}" class="btn btn-default" style="margin-bottom: 10px;">
                                    <i class="fa fa-refresh"></i> Xóa bộ lọc
                                </a>
                            </form>
                        </div>

                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-sm-12 text-right">
                                @if(auth()->user() && trim(auth()->user()->vaiTro) === 'Quản trị viên')
                                    <a href="{{ route('admin.users.congdan.create') }}" class="btn btn-success">
                                        <i class="fa fa-plus"></i> Thêm công dân
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
                                        <th>Giới tính</th>
                                        <th>Ngày sinh</th>
                                        <th>Vai trò</th>
                                        <th>Mã công dân</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($congDans as $congDan)
                                    <tr>
                                        <td>{{ $congDan->IDnguoiDung }}</td>
                                        <td>{{ $congDan->hoTen ?? '-' }}</td>
                                        <td>{{ $congDan->email ?? '-' }}</td>
                                        <td>{{ $congDan->soDienThoai ?? '-' }}</td>
                                        <td>{{ $congDan->maCCCD ?? '-' }}</td>
                                        <td>{{ $congDan->gioiTinh ?? '-' }}</td>
                                        <td>{{ $congDan->ngaySinh ? \Carbon\Carbon::parse($congDan->ngaySinh)->format('d/m/Y') : '-' }}</td>
                                        <td><span class="badge bg-info">{{ $congDan->vaiTro ?? '-' }}</span></td>
                                        <td>{{ $congDan->IDCD ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.congdan.show', $congDan->IDnguoiDung) }}"
                                               class="btn btn-xs btn-info"
                                               title="Xem công dân">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @if(auth()->user() && trim(auth()->user()->vaiTro) === 'Quản trị viên')
                                                <a href="{{ route('admin.users.congdan.edit', $congDan->IDnguoiDung) }}"
                                                   class="btn btn-xs btn-primary"
                                                   title="Sửa công dân">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.users.congdan.destroy', $congDan->IDnguoiDung) }}"
                                                      method="POST"
                                                      style="display:inline-block;"
                                                      onsubmit="return confirm('Bạn chắc chắn muốn xóa công dân này?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-danger" title="Xóa công dân">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="text-center">
                            {{ $congDans->links() }}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

