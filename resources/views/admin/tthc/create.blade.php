@extends('admin.layout')

@section('title', 'Thêm Thủ tục Hành chính')

@section('content')
@php
    $cachThucHienOld = old('cachThucHien');
    if (empty($cachThucHienOld) || !is_array($cachThucHienOld)) {
        $cachThucHienOld = [['kenh' => '', 'thoiHanGiaiQuyet' => '', 'thoiHan' => '', 'moTaPhiLePhi' => '', 'moTa' => '']];
    }

    $lePhiOld = old('lePhi');
    if (empty($lePhiOld) || !is_array($lePhiOld)) {
        $lePhiOld = [['loaiLePhi' => '', 'soTien' => '', 'batBuoc' => 'Không', 'moTa' => '']];
    }

    $thanhPhanOld = old('thanhPhanHoSo');
    if (empty($thanhPhanOld) || !is_array($thanhPhanOld)) {
        $thanhPhanOld = [['tenThanhPhan' => '', 'giayTo' => []]];
    }

    $giayToOptionsHtml = $giayTos->map(function ($giay) {
        $label = \Illuminate\Support\Str::limit($giay->tenGiayTo, 120);
        return '<option value="'.$giay->maGiayTo.'">'.e($label).'</option>';
    })->implode('');
@endphp
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
                                        <label for="doiTuongThucHien">Đối tượng thực hiện (mô tả) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="doiTuongThucHien" name="doiTuongThucHien"
                                               value="{{ old('doiTuongThucHien') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Liên kết đối tượng thực hiện trong hệ thống</label>
                                        <select class="form-control" name="doiTuongThucHienThem[]" multiple>
                                            @foreach($doiTuongs as $doiTuong)
                                                <option value="{{ $doiTuong->maDoiTuong }}"
                                                    {{ collect(old('doiTuongThucHienThem', []))->contains($doiTuong->maDoiTuong) ? 'selected' : '' }}>
                                                    {{ $doiTuong->tenDoiTuong }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="help-block">Giữ Ctrl hoặc Cmd để chọn nhiều mục.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="coQuanThucHien">Cơ quan thực hiện <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="coQuanThucHien" name="coQuanThucHien"
                                               value="{{ old('coQuanThucHien') }}" required>
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
                            </div>

                            <hr>

                            <div class="panel panel-info">
                                <div class="panel-heading clearfix">
                                    <strong>Cách thức thực hiện</strong>
                                    <button type="button" class="btn btn-xs btn-success pull-right" id="addCachThucHienBtn">
                                        <i class="fa fa-plus"></i> Thêm kênh
                                    </button>
                                </div>
                                <div class="panel-body" id="cachThucHienContainer" data-count="{{ count($cachThucHienOld) }}">
                                    @foreach($cachThucHienOld as $idx => $cach)
                                        @php $cach = (array)$cach; @endphp
                                        <div class="cach-thuc-hien-item panel panel-default" data-index="{{ $idx }}">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Kênh <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="cachThucHien[{{ $idx }}][kenh]"
                                                                   value="{{ $cach['kenh'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Thời hạn (số ngày)</label>
                                                            <input type="number" min="0" class="form-control" name="cachThucHien[{{ $idx }}][thoiHan]"
                                                                   value="{{ $cach['thoiHan'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Thời hạn giải quyết (mô tả)</label>
                                                            <input type="text" class="form-control" name="cachThucHien[{{ $idx }}][thoiHanGiaiQuyet]"
                                                                   value="{{ $cach['thoiHanGiaiQuyet'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Mô tả phí/lệ phí</label>
                                                            <textarea class="form-control" rows="2" name="cachThucHien[{{ $idx }}][moTaPhiLePhi]">{{ $cach['moTaPhiLePhi'] ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Mô tả chi tiết</label>
                                                            <textarea class="form-control" rows="2" name="cachThucHien[{{ $idx }}][moTa]">{{ $cach['moTa'] ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-xs btn-danger remove-cach-item">
                                                    <i class="fa fa-trash"></i> Xóa kênh
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading clearfix">
                                    <strong>Lệ phí/Phí áp dụng</strong>
                                    <button type="button" class="btn btn-xs btn-success pull-right" id="addLePhiBtn">
                                        <i class="fa fa-plus"></i> Thêm lệ phí
                                    </button>
                                </div>
                                <div class="panel-body" id="lePhiContainer" data-count="{{ count($lePhiOld) }}">
                                    @foreach($lePhiOld as $idx => $lePhi)
                                        @php $lePhi = (array)$lePhi; @endphp
                                        <div class="le-phi-item panel panel-default" data-index="{{ $idx }}">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Loại lệ phí</label>
                                                            <input type="text" class="form-control" name="lePhi[{{ $idx }}][loaiLePhi]"
                                                                   value="{{ $lePhi['loaiLePhi'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Số tiền (VNĐ)</label>
                                                            <input type="number" min="0" class="form-control" name="lePhi[{{ $idx }}][soTien]"
                                                                   value="{{ $lePhi['soTien'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Bắt buộc</label>
                                                            <select class="form-control" name="lePhi[{{ $idx }}][batBuoc]">
                                                                <option value="">-- Chọn --</option>
                                                                <option value="Có" {{ ($lePhi['batBuoc'] ?? '') === 'Có' ? 'selected' : '' }}>Có</option>
                                                                <option value="Không" {{ ($lePhi['batBuoc'] ?? '') === 'Không' ? 'selected' : '' }}>Không</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Ghi chú</label>
                                                            <input type="text" class="form-control" name="lePhi[{{ $idx }}][moTa]"
                                                                   value="{{ $lePhi['moTa'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-xs btn-danger remove-le-phi-item">
                                                    <i class="fa fa-trash"></i> Xóa dòng
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading clearfix">
                                    <strong>Thành phần hồ sơ &amp; Giấy tờ</strong>
                                    <button type="button" class="btn btn-xs btn-success pull-right" id="addThanhPhanBtn">
                                        <i class="fa fa-plus"></i> Thêm thành phần
                                    </button>
                                </div>
                                <div class="panel-body" id="thanhPhanContainer" data-count="{{ count($thanhPhanOld) }}">
                                    @foreach($thanhPhanOld as $tpIndex => $tp)
                                        @php
                                            $tp = (array)$tp;
                                            $giayList = $tp['giayTo'] ?? [];
                                            if (empty($giayList) || !is_array($giayList)) {
                                                $giayList = [['maGiayTo' => '', 'soLuongBanChinh' => '', 'soLuongBanSao' => '']];
                                            }
                                        @endphp
                                        <div class="thanh-phan-item panel panel-default" data-tp-index="{{ $tpIndex }}">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label>Tên thành phần</label>
                                                    <input type="text" class="form-control" name="thanhPhanHoSo[{{ $tpIndex }}][tenThanhPhan]"
                                                           value="{{ $tp['tenThanhPhan'] ?? '' }}">
                                                </div>
                                                <div class="giay-to-list" data-count="{{ count($giayList) }}">
                                                    @foreach($giayList as $giayIndex => $giay)
                                                        @php $giay = (array)$giay; @endphp
                                                        <div class="giay-to-row row" data-giay-index="{{ $giayIndex }}">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Giấy tờ</label>
                                                                    <select class="form-control"
                                                                            name="thanhPhanHoSo[{{ $tpIndex }}][giayTo][{{ $giayIndex }}][maGiayTo]">
                                                                        <option value="">-- Chọn giấy tờ --</option>
                                                                        @foreach($giayTos as $giayToOption)
                                                                            <option value="{{ $giayToOption->maGiayTo }}"
                                                                                {{ ($giay['maGiayTo'] ?? '') == $giayToOption->maGiayTo ? 'selected' : '' }}>
                                                                                {{ \Illuminate\Support\Str::limit($giayToOption->tenGiayTo, 90) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label>Bản chính</label>
                                                                    <input type="number" min="0" class="form-control"
                                                                           name="thanhPhanHoSo[{{ $tpIndex }}][giayTo][{{ $giayIndex }}][soLuongBanChinh]"
                                                                           value="{{ $giay['soLuongBanChinh'] ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label>Bản sao</label>
                                                                    <input type="number" min="0" class="form-control"
                                                                           name="thanhPhanHoSo[{{ $tpIndex }}][giayTo][{{ $giayIndex }}][soLuongBanSao]"
                                                                           value="{{ $giay['soLuongBanSao'] ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 text-right">
                                                                <button type="button" class="btn btn-xs btn-danger remove-giay-to">
                                                                    <i class="fa fa-trash"></i> Xóa giấy tờ
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-xs btn-info add-giay-to">
                                                    <i class="fa fa-plus"></i> Thêm giấy tờ
                                                </button>
                                                <button type="button" class="btn btn-xs btn-danger remove-thanh-phan">
                                                    <i class="fa fa-trash"></i> Xóa thành phần
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="form_cau_hinh">Cấu hình form trực tuyến (JSON)</label>
                                <textarea class="form-control" id="form_cau_hinh" name="form_cau_hinh" rows="6"
                                          placeholder='Ví dụ: {"fields":[...] }'>{{ old('form_cau_hinh') }}</textarea>
                                <small class="help-block">Nội dung phải là JSON hợp lệ, dùng cho biểu mẫu nộp hồ sơ trực tuyến.</small>
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

<div id="tthc-templates" style="display:none;">
    <template id="cachThucHienTemplate">
        <div class="cach-thuc-hien-item panel panel-default" data-index="__INDEX__">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kênh <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="cachThucHien[__INDEX__][kenh]">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Thời hạn (số ngày)</label>
                            <input type="number" min="0" class="form-control" name="cachThucHien[__INDEX__][thoiHan]">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Thời hạn giải quyết (mô tả)</label>
                            <input type="text" class="form-control" name="cachThucHien[__INDEX__][thoiHanGiaiQuyet]">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mô tả phí/lệ phí</label>
                            <textarea class="form-control" rows="2" name="cachThucHien[__INDEX__][moTaPhiLePhi]"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mô tả chi tiết</label>
                            <textarea class="form-control" rows="2" name="cachThucHien[__INDEX__][moTa]"></textarea>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-xs btn-danger remove-cach-item">
                    <i class="fa fa-trash"></i> Xóa kênh
                </button>
            </div>
        </div>
    </template>

    <template id="lePhiTemplate">
        <div class="le-phi-item panel panel-default" data-index="__INDEX__">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Loại lệ phí</label>
                            <input type="text" class="form-control" name="lePhi[__INDEX__][loaiLePhi]">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Số tiền (VNĐ)</label>
                            <input type="number" min="0" class="form-control" name="lePhi[__INDEX__][soTien]">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Bắt buộc</label>
                            <select class="form-control" name="lePhi[__INDEX__][batBuoc]">
                                <option value="">-- Chọn --</option>
                                <option value="Có">Có</option>
                                <option value="Không">Không</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ghi chú</label>
                            <input type="text" class="form-control" name="lePhi[__INDEX__][moTa]">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-xs btn-danger remove-le-phi-item">
                    <i class="fa fa-trash"></i> Xóa dòng
                </button>
            </div>
        </div>
    </template>

    <template id="thanhPhanTemplate">
        <div class="thanh-phan-item panel panel-default" data-tp-index="__INDEX__">
            <div class="panel-body">
                <div class="form-group">
                    <label>Tên thành phần</label>
                    <input type="text" class="form-control" name="thanhPhanHoSo[__INDEX__][tenThanhPhan]">
                </div>
                <div class="giay-to-list" data-count="0"></div>
                <button type="button" class="btn btn-xs btn-info add-giay-to">
                    <i class="fa fa-plus"></i> Thêm giấy tờ
                </button>
                <button type="button" class="btn btn-xs btn-danger remove-thanh-phan">
                    <i class="fa fa-trash"></i> Xóa thành phần
                </button>
            </div>
        </div>
    </template>

    <template id="giayToTemplate">
        <div class="giay-to-row row" data-giay-index="__GIAY_INDEX__">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Giấy tờ</label>
                    <select class="form-control" name="thanhPhanHoSo[__TP_INDEX__][giayTo][__GIAY_INDEX__][maGiayTo]">
                        <option value="">-- Chọn giấy tờ --</option>
                        __GIAY_OPTIONS__
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Bản chính</label>
                    <input type="number" min="0" class="form-control"
                           name="thanhPhanHoSo[__TP_INDEX__][giayTo][__GIAY_INDEX__][soLuongBanChinh]">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Bản sao</label>
                    <input type="number" min="0" class="form-control"
                           name="thanhPhanHoSo[__TP_INDEX__][giayTo][__GIAY_INDEX__][soLuongBanSao]">
                </div>
            </div>
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-xs btn-danger remove-giay-to">
                    <i class="fa fa-trash"></i> Xóa giấy tờ
                </button>
            </div>
        </div>
    </template>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const giayToOptionsHtml = {!! json_encode($giayToOptionsHtml) !!};

    // Cách thức thực hiện
    const cachContainer = document.getElementById('cachThucHienContainer');
    const cachTemplate = document.getElementById('cachThucHienTemplate');
    let cachIndex = Number(cachContainer?.dataset.count || 0);

    document.getElementById('addCachThucHienBtn')?.addEventListener('click', function () {
        if (!cachContainer || !cachTemplate) return;
        const html = cachTemplate.innerHTML.replace(/__INDEX__/g, cachIndex++);
        cachContainer.insertAdjacentHTML('beforeend', html);
    });

    cachContainer?.addEventListener('click', function (event) {
        if (event.target.closest('.remove-cach-item')) {
            event.preventDefault();
            event.target.closest('.cach-thuc-hien-item')?.remove();
        }
    });

    // Lệ phí
    const lePhiContainer = document.getElementById('lePhiContainer');
    const lePhiTemplate = document.getElementById('lePhiTemplate');
    let lePhiIndex = Number(lePhiContainer?.dataset.count || 0);

    document.getElementById('addLePhiBtn')?.addEventListener('click', function () {
        if (!lePhiContainer || !lePhiTemplate) return;
        const html = lePhiTemplate.innerHTML.replace(/__INDEX__/g, lePhiIndex++);
        lePhiContainer.insertAdjacentHTML('beforeend', html);
    });

    lePhiContainer?.addEventListener('click', function (event) {
        if (event.target.closest('.remove-le-phi-item')) {
            event.preventDefault();
            event.target.closest('.le-phi-item')?.remove();
        }
    });

    // Thành phần hồ sơ & Giấy tờ
    const thanhPhanContainer = document.getElementById('thanhPhanContainer');
    const thanhPhanTemplate = document.getElementById('thanhPhanTemplate');
    const giayToTemplate = document.getElementById('giayToTemplate');
    let thanhPhanIndex = Number(thanhPhanContainer?.dataset.count || 0);

    function addGiayToRow(parentIndex, holder) {
        if (!giayToTemplate) return;
        let giayIndex = Number(holder.dataset.count || 0);
        let html = giayToTemplate.innerHTML
            .replace(/__TP_INDEX__/g, parentIndex)
            .replace(/__GIAY_INDEX__/g, giayIndex)
            .replace(/__GIAY_OPTIONS__/g, giayToOptionsHtml);
        holder.dataset.count = giayIndex + 1;
        holder.insertAdjacentHTML('beforeend', html);
    }

    document.getElementById('addThanhPhanBtn')?.addEventListener('click', function () {
        if (!thanhPhanContainer || !thanhPhanTemplate) return;
        let html = thanhPhanTemplate.innerHTML.replace(/__INDEX__/g, thanhPhanIndex);
        html = html.replace(/__INDEX__/g, thanhPhanIndex);
        thanhPhanContainer.insertAdjacentHTML('beforeend', html);
        const newest = thanhPhanContainer.querySelector(`.thanh-phan-item[data-tp-index="${thanhPhanIndex}"] .giay-to-list`);
        if (newest) {
            addGiayToRow(thanhPhanIndex, newest);
        }
        thanhPhanIndex++;
    });

    thanhPhanContainer?.addEventListener('click', function (event) {
        if (event.target.closest('.remove-thanh-phan')) {
            event.preventDefault();
            event.target.closest('.thanh-phan-item')?.remove();
            return;
        }
        if (event.target.closest('.add-giay-to')) {
            event.preventDefault();
            const block = event.target.closest('.thanh-phan-item');
            if (!block) return;
            const holder = block.querySelector('.giay-to-list');
            const parentIndex = block.dataset.tpIndex;
            if (holder && parentIndex !== undefined) {
                addGiayToRow(parentIndex, holder);
            }
            return;
        }
        if (event.target.closest('.remove-giay-to')) {
            event.preventDefault();
            event.target.closest('.giay-to-row')?.remove();
        }
    });
});
</script>
@endpush
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

