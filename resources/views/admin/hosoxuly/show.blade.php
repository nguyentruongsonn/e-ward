@extends('admin.layout')

@section('title', 'Chi tiết hồ sơ')

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-file-text"></i> Chi tiết hồ sơ: {{ $hoSo->maHSXL }}</h3>
                        <div class="mt-2">
                            <a href="{{ route('admin.hosoxuly.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Quay lại danh sách
                            </a>
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#sendMailModal">
                                <i class="fa fa-envelope"></i> Gửi mail cho chủ hồ sơ
                            </button>
                            @if($mailHistory && $mailHistory->count() > 0)
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#mailHistoryModal" style="margin-left: 10px;">
                                    <i class="fa fa-history"></i> Xem lịch sử mail ({{ $mailHistory->count() }})
                                </button>
                            @endif
                        </div>
                    </header>
                    <div class="panel-body">
                        <!-- Thông tin cơ bản -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4 class="mb-3" style="color: #32C36C; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                    <i class="fa fa-info-circle"></i> Thông tin cơ bản
                                </h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 40%; background: #f5f5f5;">Mã hồ sơ:</th>
                                        <td><strong>{{ $hoSo->maHSXL }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Thủ tục hành chính:</th>
                                        <td>{{ $hoSo->tthc->tenTTHC ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Tên chủ hồ sơ:</th>
                                        <td>{{ $hoSo->tenChuHoSo }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Đối tượng thực hiện:</th>
                                        <td>{{ $hoSo->doiTuongThucHien ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Email:</th>
                                        <td>{{ $hoSo->email }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Số điện thoại:</th>
                                        <td>{{ $hoSo->soDienThoai }}</td>
                                    </tr>
                                    @if($hoSo->congdan && $hoSo->congdan->nguoi)
                                        <tr>
                                            <th style="background: #f5f5f5;">Họ tên người dùng:</th>
                                            <td>{{ $hoSo->congdan->nguoi->hoTen ?? '-' }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-3" style="color: #32C36C; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                    <i class="fa fa-calendar"></i> Thông tin xử lý
                                </h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 40%; background: #f5f5f5;">Ngày tiếp nhận:</th>
                                        <td>{{ $hoSo->ngayTiepNhan ? $hoSo->ngayTiepNhan->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Ngày hẹn trả:</th>
                                        <td>{{ $hoSo->ngayHenTra ? $hoSo->ngayHenTra->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Ngày trả:</th>
                                        <td>{{ $hoSo->ngayTra ? $hoSo->ngayTra->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Ngày kết thúc xử lý:</th>
                                        <td>{{ $hoSo->ngayKetThucXuLy ? $hoSo->ngayKetThucXuLy->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Trạng thái:</th>
                                        <td>
                                            @if($hoSo->trangThai)
                                                <span class="badge 
                                                    @if($hoSo->maTrangThai == 8 || $hoSo->maTrangThai == 9)
                                                        bg-success
                                                    @elseif($hoSo->maTrangThai == 3 || $hoSo->maTrangThai == 4 || $hoSo->maTrangThai == 5)
                                                        bg-warning
                                                    @elseif($hoSo->maTrangThai == 2 || $hoSo->maTrangThai == 6 || $hoSo->maTrangThai == 7)
                                                        bg-danger
                                                    @else
                                                        bg-info
                                                    @endif">
                                                    {{ $hoSo->trangThai->tenTrangThai }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Đơn vị xử lý:</th>
                                        <td>{{ $hoSo->donViXuLy ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Hình thức:</th>
                                        <td>{{ $hoSo->hinhThuc ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f5f5f5;">Lệ phí:</th>
                                        <td>{{ $hoSo->lePhi ? number_format($hoSo->lePhi, 0, ',', '.') . ' đ' : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Dữ liệu form -->
                        @if($hoSo->dulieu && is_array($hoSo->dulieu) && count($hoSo->dulieu) > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3" style="color: #32C36C; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                        <i class="fa fa-database"></i> Dữ liệu đã nộp
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 30%;">Trường dữ liệu</th>
                                                    <th>Giá trị</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($hoSo->dulieu as $key => $value)
                                                    <tr>
                                                        <td><strong>{{ $key }}</strong></td>
                                                        <td>
                                                            @if($key == 'muc_le_phi' && is_array($value))
                                                                @foreach($value as $val)
                                                                    @if(is_array($val))
                                                                        {{ json_encode($val, JSON_UNESCAPED_UNICODE) }}
                                                                    @else
                                                                        {{ number_format((float)$val, 0, ',', '.') }} đ
                                                                    @endif
                                                                    <br>
                                                                @endforeach
                                                            @elseif($key == 'le_phi_so_luong' && is_array($value))
                                                                @foreach($value as $val)
                                                                    @if(is_array($val))
                                                                        {{ json_encode($val, JSON_UNESCAPED_UNICODE) }}
                                                                    @else
                                                                        {{ $val }}
                                                                    @endif
                                                                    <br>
                                                                @endforeach
                                                            @elseif(is_array($value))
                                                                @foreach($value as $val)
                                                                    @if(is_array($val))
                                                                        {{ json_encode($val, JSON_UNESCAPED_UNICODE) }}
                                                                    @else
                                                                        {{ $val }}
                                                                    @endif
                                                                    <br>
                                                                @endforeach
                                                            @elseif(is_string($value) && (str_starts_with($value, 'http') || str_contains($value, '/storage/')))
                                                                <a href="{{ $value }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fa fa-download"></i> Xem/Tải file
                                                                </a>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Tài liệu đã nộp -->
                        @if($taiLieu && $taiLieu->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3" style="color: #32C36C; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                        <i class="fa fa-file"></i> Tài liệu đã nộp
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Tên tài liệu</th>
                                                    <th>Loại tài liệu</th>
                                                    <th>File</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($taiLieu as $index => $tl)
                                                    @php
                                                        $giayTo = DB::table('giayto')->where('maGiayTo', $tl->maGiayTo)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $giayTo->tenGiayTo ?? ($tl->tenTep ?? '-') }}</td>
                                                        <td>{{ $giayTo->loaiGiayTo ?? '-' }}</td>
                                                        <td>
                                                            @if($tl->duongDan)
                                                                <a href="{{ asset('storage/' . $tl->duongDan) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fa fa-download"></i> Tải xuống
                                                                </a>
                                                                <small class="d-block text-muted mt-1">
                                                                    {{ $tl->tenTep ?? '' }} 
                                                                    @if($tl->kichThuoc)
                                                                        ({{ number_format($tl->kichThuoc / 1024, 2) }} KB)
                                                                    @endif
                                                                </small>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Lịch sử thanh toán -->
                        @if($lichSuThanhToan && $lichSuThanhToan->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3" style="color: #32C36C; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                        <i class="fa fa-money"></i> Lịch sử thanh toán
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Mã GD</th>
                                                    <th>Số GD</th>
                                                    <th>Loại GD</th>
                                                    <th>Ngày GD</th>
                                                    <th>Số tiền</th>
                                                    <th>Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($lichSuThanhToan as $gd)
                                                    <tr>
                                                        <td>{{ $gd->maGD }}</td>
                                                        <td>{{ $gd->soGD ?? '-' }}</td>
                                                        <td>{{ $gd->loaiGD ?? '-' }}</td>
                                                        <td>{{ $gd->ngayGD ? \Carbon\Carbon::parse($gd->ngayGD)->format('d/m/Y H:i') : '-' }}</td>
                                                        <td>{{ $gd->soTien ? number_format($gd->soTien, 0, ',', '.') . ' đ' : '-' }}</td>
                                                        <td>{{ $gd->trangThai ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Ghi chú -->
                        @if($hoSo->ghiChu)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3" style="color: #32C36C; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                        <i class="fa fa-sticky-note"></i> Ghi chú
                                    </h4>
                                    <div class="alert alert-info">
                                        {{ $hoSo->ghiChu }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Thông tin trả -->
                        @if($hoSo->thongTinTra)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3" style="color: #32C36C; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                        <i class="fa fa-info"></i> Thông tin trả
                                    </h4>
                                    <div class="alert alert-warning">
                                        {{ $hoSo->thongTinTra }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->

<!-- Modal Gửi mail -->
<div class="modal fade" id="sendMailModal" tabindex="-1" aria-labelledby="sendMailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendMailModalLabel">
                    <i class="fa fa-envelope"></i> Gửi mail cho chủ hồ sơ
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="sendMailForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>Người nhận:</strong></label>
                        <input type="text" class="form-control" value="{{ $hoSo->tenChuHoSo }} ({{ $hoSo->email }})" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Mã hồ sơ:</strong></label>
                        <input type="text" class="form-control" value="{{ $hoSo->maHSXL }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Loại mail:</strong></label>
                        <select name="loai_mail" id="loai_mail" class="form-control" required>
                            <option value="lien_lac">Liên lạc với chủ hồ sơ</option>
                            <option value="bo_sung">Yêu cầu bổ sung hồ sơ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Tiêu đề:</strong></label>
                        <input type="text" name="subject" id="mail_subject" class="form-control" 
                               placeholder="Nhập tiêu đề email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Nội dung:</strong></label>
                        <textarea name="content" id="mail_content" class="form-control" rows="8" 
                                  placeholder="Nhập nội dung email..." required></textarea>
                        <small class="text-muted">Bạn có thể sử dụng HTML để định dạng nội dung</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-paper-plane"></i> Gửi mail
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Lịch sử mail -->
<div class="modal fade" id="mailHistoryModal" tabindex="-1" aria-labelledby="mailHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 90%; width: 1200px;">
        <div class="modal-content" style="min-height: 600px;">
            <div class="modal-header" style="background: #32C36C; color: white; padding: 20px;">
                <h4 class="modal-title" id="mailHistoryModalLabel" style="font-size: 20px; font-weight: bold; margin: 0;">
                    <i class="fa fa-history"></i> Lịch sử mail đã gửi cho chủ hồ sơ
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 28px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 25px; max-height: 70vh; overflow-y: auto;">
                @if($mailHistory && $mailHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" style="font-size: 14px;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="width: 12%; padding: 12px; font-weight: bold; font-size: 14px;">Thời gian</th>
                                    <th style="width: 15%; padding: 12px; font-weight: bold; font-size: 14px;">Loại mail</th>
                                    <th style="width: 28%; padding: 12px; font-weight: bold; font-size: 14px;">Tiêu đề</th>
                                    <th style="width: 20%; padding: 12px; font-weight: bold; font-size: 14px;">Email</th>
                                    <th style="width: 25%; padding: 12px; font-weight: bold; font-size: 14px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mailHistory as $mail)
                                    <tr style="background-color: {{ $mail->direction == 'incoming' ? '#e8f5e9' : '#ffffff' }}; border-bottom: 2px solid #e0e0e0;">
                                        <td style="padding: 15px; font-size: 14px; vertical-align: middle;">{{ $mail->sent_at->format('d/m/Y H:i') }}</td>
                                        <td style="padding: 15px; vertical-align: middle;">
                                            @if($mail->direction == 'incoming')
                                                <span class="badge badge-success" style="font-size: 13px; padding: 8px 12px;">
                                                    <i class="fa fa-arrow-left"></i> Công dân trả lời
                                                </span>
                                            @else
                                                <span class="badge {{ $mail->loai_mail == 'bo_sung' ? 'badge-warning' : 'badge-info' }}" style="font-size: 13px; padding: 8px 12px;">
                                                    <i class="fa fa-arrow-right"></i> {{ $mail->loai_mail == 'bo_sung' ? 'Bổ sung hồ sơ' : 'Liên lạc' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td style="padding: 15px; font-size: 14px; vertical-align: middle; font-weight: 500;">{{ strlen($mail->subject) > 60 ? substr($mail->subject, 0, 60) . '...' : $mail->subject }}</td>
                                        <td style="padding: 15px; font-size: 14px; vertical-align: middle;">{{ $mail->email }}</td>
                                        <td style="padding: 15px; vertical-align: middle;">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="viewMailContent({{ $mail->id }})" style="font-size: 13px; padding: 8px 15px;">
                                                <i class="fa fa-eye"></i> Xem nội dung
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Mail content hidden -->
                                    <tr id="mail-content-{{ $mail->id }}" style="display: none;">
                                        <td colspan="5" style="padding: 20px; background: #fafafa;">
                                            <div class="card" style="border: 2px solid #32C36C; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                <div class="card-header" style="background: #32C36C; color: white; padding: 18px; border-radius: 6px 6px 0 0;">
                                                    @if($mail->direction == 'incoming')
                                                        <h5 style="margin: 0 0 12px 0; font-size: 16px; font-weight: bold;">
                                                            <i class="fa fa-arrow-left"></i> Email từ công dân
                                                        </h5>
                                                    @else
                                                        <h5 style="margin: 0 0 12px 0; font-size: 16px; font-weight: bold;">
                                                            <i class="fa fa-arrow-right"></i> Email gửi đi
                                                        </h5>
                                                    @endif
                                                    <div style="font-size: 14px; line-height: 1.8;">
                                                        <div><strong>Tiêu đề:</strong> {{ $mail->subject }}</div>
                                                        @if($mail->direction == 'outgoing')
                                                            <div><strong>Loại:</strong> {{ $mail->loai_mail == 'bo_sung' ? 'Yêu cầu bổ sung hồ sơ' : 'Liên lạc với chủ hồ sơ' }}</div>
                                                        @endif
                                                        <div><strong>{{ $mail->direction == 'incoming' ? 'Từ' : 'Gửi đến' }}:</strong> {{ $mail->email }}</div>
                                                        <div><strong>Thời gian:</strong> {{ $mail->sent_at->format('d/m/Y H:i:s') }}</div>
                                                    </div>
                                                </div>
                                                <div class="card-body" style="padding: 20px; max-height: 500px; overflow-y: auto; background: white;">
                                                    <strong style="font-size: 15px; color: #333; display: block; margin-bottom: 12px;">Nội dung:</strong>
                                                    <div style="padding: 18px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #32C36C; white-space: pre-wrap; font-size: 14px; line-height: 1.8; color: #333; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin-bottom: 15px;">{{ $mail->content }}</div>
                                                    @if($mail->direction == 'incoming')
                                                        <div style="text-align: right; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                                            <button type="button" class="btn btn-success" onclick="replyToMail({{ $mail->id }}, '{{ addslashes($mail->subject) }}', '{{ addslashes($mail->email) }}')" style="font-size: 14px; padding: 10px 20px;">
                                                                <i class="fa fa-reply"></i> Trả lời mail
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info" style="font-size: 15px; padding: 20px; text-align: center;">
                        <i class="fa fa-info-circle" style="font-size: 18px;"></i> Chưa có lịch sử gửi mail nào.
                    </div>
                @endif
            </div>
            <div class="modal-footer" style="padding: 20px; background: #f8f9fa; border-top: 2px solid #e0e0e0;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="font-size: 14px; padding: 10px 25px;">
                    <i class="fa fa-times"></i> Đóng
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function viewMailContent(mailId) {
    const contentRow = document.getElementById('mail-content-' + mailId);
    if (contentRow.style.display === 'none') {
        contentRow.style.display = 'table-row';
    } else {
        contentRow.style.display = 'none';
    }
}

function replyToMail(mailId, originalSubject, email) {
    // Đóng modal lịch sử mail trước
    $('#mailHistoryModal').modal('hide');
    
    // Đợi modal lịch sử mail đóng xong rồi mới mở modal gửi mail
    $('#mailHistoryModal').on('hidden.bs.modal', function() {
        // Mở modal gửi mail
        $('#sendMailModal').modal('show');
        
        // Điền thông tin
        const subjectInput = document.getElementById('mail_subject');
        const contentTextarea = document.getElementById('mail_content');
        const loaiMailSelect = document.getElementById('loai_mail');
        
        // Điền subject với "Re:" nếu chưa có
        let replySubject = originalSubject;
        if (!replySubject.toLowerCase().startsWith('re:')) {
            replySubject = 'Re: ' + replySubject;
        }
        subjectInput.value = replySubject;
        
        // Đặt loại mail là liên lạc
        loaiMailSelect.value = 'lien_lac';
        
        // Điền nội dung mẫu
        const currentDate = new Date().toLocaleDateString('vi-VN');
        contentTextarea.value = 'Kính gửi ' + email + ',\n\n' +
            'Cảm ơn bạn đã phản hồi.\n\n' +
            '[Vui lòng nhập nội dung trả lời]\n\n' +
            'Trân trọng!';
        
        // Focus vào textarea
        setTimeout(() => {
            contentTextarea.focus();
            // Đặt cursor ở vị trí bắt đầu nội dung cần nhập
            const startPos = contentTextarea.value.indexOf('[Vui lòng nhập nội dung trả lời]');
            if (startPos !== -1) {
                contentTextarea.setSelectionRange(startPos, startPos);
            }
        }, 300);
        
        // Xóa event listener để tránh lặp lại
        $('#mailHistoryModal').off('hidden.bs.modal');
    });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sendMailForm = document.getElementById('sendMailForm');
    const loaiMailSelect = document.getElementById('loai_mail');
    const subjectInput = document.getElementById('mail_subject');
    const contentTextarea = document.getElementById('mail_content');
    
    // Tự động điền tiêu đề và nội dung mẫu khi chọn loại mail
    loaiMailSelect.addEventListener('change', function() {
        if (this.value === 'bo_sung') {
            subjectInput.value = 'Yêu cầu bổ sung hồ sơ - {{ $hoSo->maHSXL }}';
            contentTextarea.value = 'Kính gửi {{ $hoSo->tenChuHoSo }},\n\nHồ sơ của bạn (Mã: {{ $hoSo->maHSXL }}) cần được bổ sung thêm thông tin/tài liệu sau:\n\n[Vui lòng nhập chi tiết yêu cầu bổ sung]\n\nVui lòng bổ sung và gửi lại trong thời gian sớm nhất.\n\nTrân trọng!';
        } else {
            subjectInput.value = 'Thông báo về hồ sơ - {{ $hoSo->maHSXL }}';
            contentTextarea.value = 'Kính gửi {{ $hoSo->tenChuHoSo }},\n\nVề hồ sơ của bạn (Mã: {{ $hoSo->maHSXL }}):\n\n[Vui lòng nhập nội dung thông báo]\n\nTrân trọng!';
        }
    });
    
    // Gửi mail
    sendMailForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang gửi...';
        
        fetch('{{ route('admin.hosoxuly.send-mail', $hoSo->maHSXL) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Đã gửi mail thành công đến ' + data.email);
                // Đóng modal
                $('#sendMailModal').modal('hide');
                // Reset form
                sendMailForm.reset();
                // Reload trang để hiển thị badge "Đã gửi mail"
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể gửi mail'));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi gửi mail');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
});
</script>
@endpush

