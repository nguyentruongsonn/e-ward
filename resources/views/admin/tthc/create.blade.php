@extends('admin.layout')

@section('title', 'Thêm Thủ tục Hành chính')

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-plus"></i> Thêm Thủ tục Hành chính mới</h3>
                    </header>
                    <div class="panel-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.tthc.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="tenTTHC">Tên thủ tục hành chính <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tenTTHC" name="tenTTHC" 
                                               value="{{ old('tenTTHC') }}" required maxlength="500">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="maLinhVuc">Lĩnh vực <span class="text-danger">*</span></label>
                                        <select class="form-control" id="maLinhVuc" name="maLinhVuc" required>
                                            <option value="">-- Chọn lĩnh vực --</option>
                                            @foreach($linhVucs as $lv)
                                                <option value="{{ $lv->maLinhVuc }}" {{ old('maLinhVuc') == $lv->maLinhVuc ? 'selected' : '' }}>
                                                    {{ $lv->tenLinhVuc }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="maQuayLamViec">Quầy làm việc</label>
                                        <select class="form-control" id="maQuayLamViec" name="maQuayLamViec">
                                            <option value="">-- Chọn quầy làm việc --</option>
                                            @foreach($quayLamViecs as $quay)
                                                <option value="{{ $quay->maQuayLamViec }}" {{ old('maQuayLamViec') == $quay->maQuayLamViec ? 'selected' : '' }}>
                                                    {{ $quay->tenQuayLamViec }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doiTuongThucHien">Đối tượng thực hiện <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="doiTuongThucHien" name="doiTuongThucHien" 
                                               value="{{ old('doiTuongThucHien') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="coQuanThucHien">Cơ quan thực hiện <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="coQuanThucHien" name="coQuanThucHien" 
                                               value="{{ old('coQuanThucHien') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="trinhTuThucHien">Trình tự thực hiện <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="trinhTuThucHien" name="trinhTuThucHien" rows="5" required>{{ old('trinhTuThucHien') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="yeuCauDieuKien">Yêu cầu điều kiện <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="yeuCauDieuKien" name="yeuCauDieuKien" rows="5" required>{{ old('yeuCauDieuKien') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="canCuPhapLy">Căn cứ pháp lý <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="canCuPhapLy" name="canCuPhapLy" rows="5" required>{{ old('canCuPhapLy') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ketQuaThucHien">Kết quả thực hiện <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="ketQuaThucHien" name="ketQuaThucHien" 
                                               value="{{ old('ketQuaThucHien') }}" required maxlength="500">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trangThai">Trạng thái</label>
                                        <select class="form-control" id="trangThai" name="trangThai">
                                            <option value="Chờ công khai" {{ old('trangThai', 'Chờ công khai') == 'Chờ công khai' ? 'selected' : '' }}>Chờ công khai</option>
                                            <option value="Công khai" {{ old('trangThai') == 'Công khai' ? 'selected' : '' }}>Công khai</option>
                                            <option value="Bãi bỏ" {{ old('trangThai') == 'Bãi bỏ' ? 'selected' : '' }}>Bãi bỏ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Lưu
                                </button>
                                <a href="{{ route('admin.tthc.index') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

