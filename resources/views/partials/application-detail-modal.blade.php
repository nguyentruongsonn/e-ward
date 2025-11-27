{{-- Application Detail Modal for Citizens --}}
<div class="modal fade" id="applicationDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa fa-file-text-o"></i> Chi tiết hồ sơ: <span id="modal-ma-hsxl"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body-content">
                <div class="text-center py-5">
                    <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">Đang tải thông tin...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open application detail modal
function openApplicationDetail(maHSXL, maTrangThai, ngayTra) {
    $('#modal-ma-hsxl').text(maHSXL);
    $('#applicationDetailModal').modal('show');
    
    // Load application details via AJAX
    $.ajax({
        url: `/profile/hoso/${maHSXL}`,
        method: 'GET',
        success: function(data) {
            renderApplicationDetail(data, maTrangThai, ngayTra);
        },
        error: function() {
            $('#modal-body-content').html(`
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> Không thể tải thông tin hồ sơ
                </div>
            `);
        }
    });
}

function renderApplicationDetail(data, maTrangThai, ngayTra) {
    let html = `
        <div class="container-fluid">
            <!-- Basic Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fa fa-info-circle"></i> Thông tin cơ bản</h6>
                    <table class="table table-sm">
                        <tr><td class="font-weight-bold">Tên dịch vụ:</td><td>${data.tenTTHC || '-'}</td></tr>
                        <tr><td class="font-weight-bold">Người nộp:</td><td>${data.tenChuHoSo || '-'}</td></tr>
                        <tr><td class="font-weight-bold">Email:</td><td>${data.email || '-'}</td></tr>
                        <tr><td class="font-weight-bold">Số điện thoại:</td><td>${data.soDienThoai || '-'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fa fa-calendar"></i> Thời gian</h6>
                    <table class="table table-sm">
                        <tr><td class="font-weight-bold">Ngày tiếp nhận:</td><td>${data.ngayTiepNhan || '-'}</td></tr>
                        <tr><td class="font-weight-bold">Ngày hẹn trả:</td><td>${data.ngayHenTra || '-'}</td></tr>
                        <tr><td class="font-weight-bold">Ngày trả:</td><td>${data.ngayTra || '-'}</td></tr>
                        <tr><td class="font-weight-bold">Lệ phí:</td><td class="text-success font-weight-bold">${data.lePhi ? new Intl.NumberFormat('vi-VN').format(data.lePhi) + ' VNĐ' : '-'}</td></tr>
                    </table>
                </div>
            </div>
    `;
    
    // Show supplement upload form if status = 5
    if (maTrangThai == 5) {
        html += renderSupplementUploadForm(data.maHSXL);
    }
    
    // Show rating form if status = 10 and within 10 days
    if (maTrangThai == 10 && ngayTra) {
        const daysSince = Math.floor((new Date() - new Date(ngayTra)) / (1000 * 60 * 60 * 24));
        if (daysSince <= 10) {
            html += renderRatingForm(data.maHSXL);
        }
    }
    
    html += `</div>`;
    
    $('#modal-body-content').html(html);
}

function renderSupplementUploadForm(maHSXL) {
    return `
        <div class="alert alert-warning">
            <h5><i class="fa fa-exclamation-triangle"></i> Hồ sơ yêu cầu bổ sung giấy tờ</h5>
            <p>Vui lòng tải lên các giấy tờ cần bổ sung để hồ sơ được xử lý tiếp.</p>
        </div>
        
        <form id="supplementUploadForm" action="/profile/application/${maHSXL}/upload-supplement" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
            
            <div id="file-upload-container">
                <div class="file-upload-item mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Loại giấy tờ</label>
                            <select name="maGiayTo[]" class="form-control" required>
                                <option value="">-- Chọn loại giấy tờ --</option>
                                <!-- Will be populated from yeu_cau_bo_sung -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>File (PDF, JPG, PNG - tối đa 10MB)</label>
                            <input type="file" name="files[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addFileUploadRow()">
                <i class="fa fa-plus"></i> Thêm file
            </button>
            
            <div class="text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-upload"></i> Nộp bổ sung
                </button>
            </div>
        </form>
    `;
}

function renderRatingForm(maHSXL) {
    return `
        <div class="card border-success mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fa fa-star"></i> Đánh giá chất lượng dịch vụ</h5>
            </div>
            <div class="card-body">
                <p>Hồ sơ của bạn đã được xử lý xong. Vui lòng đánh giá chất lượng dịch vụ!</p>
                
                <form id="ratingForm">
                    <div class="form-group">
                        <label>Đánh giá của bạn:</label>
                        <div class="star-rating">
                            ${[5,4,3,2,1].map(i => `
                                <input type="radio" id="star${i}" name="soDiem" value="${i}" required>
                                <label for="star${i}" title="${i} sao">
                                    <i class="fa fa-star"></i>
                                </label>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Nhận xét (tùy chọn):</label>
                        <textarea name="nhanXet" class="form-control" rows="4" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-paper-plane"></i> Gửi đánh giá
                    </button>
                </form>
            </div>
        </div>
        
        <style>
            .star-rating {
                direction: rtl;
                display: inline-flex;
                font-size: 2rem;
            }
            .star-rating input[type="radio"] {
                display: none;
            }
            .star-rating label {
                color: #ddd;
                cursor: pointer;
                padding: 0 5px;
            }
            .star-rating input[type="radio"]:checked ~ label,
            .star-rating label:hover,
            .star-rating label:hover ~ label {
                color: #17a2b8;
            }
        </style>
        
        <script>
            $('#ratingForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    soDiem: $('input[name="soDiem"]:checked').val(),
                    nhanXet: $('textarea[name="nhanXet"]').val()
                };
                
                $.ajax({
                    url: '/profile/application/${maHSXL}/rate',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#applicationDetailModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                });
            });
        </script>
    `;
}

function addFileUploadRow() {
    const html = `
        <div class="file-upload-item mb-3">
            <div class="row">
                <div class="col-md-6">
                    <label>Loại giấy tờ</label>
                    <select name="maGiayTo[]" class="form-control" required>
                        <option value="">-- Chọn loại giấy tờ --</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label>File</label>
                    <input type="file" name="files[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-block" onclick="$(this).closest('.file-upload-item').remove()">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    $('#file-upload-container').append(html);
}
</script>
