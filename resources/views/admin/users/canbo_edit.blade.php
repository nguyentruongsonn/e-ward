@extends('admin.layout')

@section('title', 'Sửa Cán bộ')

@section('content')
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-pencil"></i> Sửa Cán bộ</h3>
                    </header>
                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form-horizontal" method="POST" action="{{ route('admin.users.canbo.update', $canBo->IDCB) }}">
                            @csrf

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Họ tên<span class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" name="hoTen" class="form-control" value="{{ old('hoTen', $canBo->hoTen) }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Email<span class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $canBo->email) }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Mật khẩu mới</label>
                                <div class="col-sm-3">
                                    <input type="password" name="password" class="form-control">
                                </div>
                                <label class="col-sm-1 control-label">Nhập lại</label>
                                <div class="col-sm-3">
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                                <p class="col-sm-offset-2 col-sm-6 help-block">Để trống nếu không đổi mật khẩu.</p>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Số điện thoại</label>
                                <div class="col-sm-6">
                                    <input type="text" name="soDienThoai" class="form-control" value="{{ old('soDienThoai', $canBo->soDienThoai) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Mã CCCD<span class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" name="maCCCD" class="form-control" value="{{ old('maCCCD', $canBo->maCCCD) }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Giới tính</label>
                                <div class="col-sm-3">
                                    <select name="gioiTinh" class="form-control">
                                        <option value="">Không xác định</option>
                                        <option value="Nam" {{ old('gioiTinh', $canBo->gioiTinh) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                        <option value="Nữ" {{ old('gioiTinh', $canBo->gioiTinh) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Ngày sinh</label>
                                <div class="col-sm-3">
                                    <input type="date" name="ngaySinh" class="form-control" value="{{ old('ngaySinh', $canBo->ngaySinh) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Quê quán</label>
                                <div class="col-sm-6">
                                    <input type="text" name="queQuan" class="form-control" value="{{ old('queQuan', $canBo->queQuan) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Nơi thường trú</label>
                                <div class="col-sm-6">
                                    <input type="text" name="noiThuongTru" class="form-control" value="{{ old('noiThuongTru', $canBo->noiThuongTru) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Nơi tạm trú</label>
                                <div class="col-sm-6">
                                    <input type="text" name="noiTamTru" class="form-control" value="{{ old('noiTamTru', $canBo->noiTamTru) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Vai trò<span class="text-danger">*</span></label>
                                <div class="col-sm-4">
                                    <select name="vaiTro" class="form-control" required>
                                        <option value="Cán bộ một cửa" {{ old('vaiTro', $canBo->vaiTro) == 'Cán bộ một cửa' ? 'selected' : '' }}>Cán bộ một cửa</option>
                                        <option value="Cán bộ thụ lý" {{ old('vaiTro', $canBo->vaiTro) == 'Cán bộ thụ lý' ? 'selected' : '' }}>Cán bộ thụ lý</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Quầy làm việc</label>
                                <div class="col-sm-4">
                                    <select name="maQuayLamViec" class="form-control">
                                        <option value="">-- Chọn quầy --</option>
                                        @foreach($quayLamViecs as $quay)
                                            <option value="{{ $quay->maQuayLamViec }}" {{ old('maQuayLamViec', $canBo->maQuayLamViec) == $quay->maQuayLamViec ? 'selected' : '' }}>
                                                {{ $quay->tenQuayLamViec }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Cập nhật
                                    </button>
                                    <a href="{{ route('admin.users.canbo') }}" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
@endsection


