<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormTrucTuyenSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('formtructuyen')->insert([
            [
                'maForm' => 1,
                'maTTHC' => 2, // Thủ tục: Đăng ký tạm trú
                'cauHinhForm' => json_encode([

                    // TÊN FORM HIỂN THỊ
                    [
                        'label' => 'Đăng ký tạm trú trực tuyến',
                        'type' => 'title',
                    ],

                    // --- CHỌN TRƯỜNG HỢP ĐĂNG KÝ ---
                    [
                        'label' => 'Trường hợp đăng ký',
                        'name' => 'truong_hop',
                        'type' => 'select',
                        'options' => [
                            'Đăng ký tạm trú theo nhân khẩu',
                            'Đăng ký tạm trú theo danh sách'
                        ]
                    ],

                    // =============================
                    // 1️⃣ TRƯỜNG HỢP: NHÂN KHẨU
                    // =============================

                    [
                        'group' => 'Thông tin người đề nghị đăng ký tạm trú',
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo nhân khẩu'],
                        'fields' => [
                            [
                                'label' => 'Người khai thông tin là',
                                'name' => 'nguoi_khai_thong_tin',
                                'type' => 'radio',
                                'options' => [
                                    'Người đăng ký tạm trú',
                                    'Khai hộ'
                                ]
                            ],
                            [
                                'label' => 'Họ và tên',
                                'name' => 'ho_ten',
                                'type' => 'text'
                            ],
                            [
                                'label' => 'Ngày tháng năm sinh',
                                'name' => 'ngay_sinh',
                                'type' => 'date'
                            ],
                            [
                                'label' => 'Giới tính',
                                'name' => 'gioi_tinh',
                                'type' => 'select',
                                'options' => ['Nam', 'Nữ', 'Khác']
                            ],
                            [
                                'label' => 'Số định danh cá nhân',
                                'name' => 'so_dinh_danh',
                                'type' => 'text'
                            ],
                            [
                                'label' => 'Số điện thoại',
                                'name' => 'so_dien_thoai',
                                'type' => 'text'
                            ],
                            [
                                'label' => 'Email',
                                'name' => 'email',
                                'type' => 'email'
                            ]
                        ]
                    ],

                    [
                        'group' => 'Thông tin đề nghị đăng ký tạm trú',
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo nhân khẩu'],
                        'fields' => [
                            ['label' => 'Tên chủ hộ', 'name' => 'ten_chu_ho', 'type' => 'text'],
                            ['label' => 'Quan hệ với chủ hộ', 'name' => 'quan_he_chu_ho', 'type' => 'text'],
                            ['label' => 'Số định danh chủ hộ', 'name' => 'so_dinh_danh_chu_ho', 'type' => 'text'],
                            ['label' => 'Nội dung đề nghị', 'name' => 'noi_dung', 'type' => 'textarea'],
                            ['label' => 'Thời hạn tạm trú đến ngày', 'name' => 'thoi_han_tam_tru', 'type' => 'date']
                        ]
                    ],

                    [
                        'group' => 'Những thành viên trong gia đình thay đổi',
                        'repeatable' => true,
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo nhân khẩu'],
                        'fields' => [
                            ['label' => 'Họ và tên', 'name' => 'ho_ten_thanh_vien', 'type' => 'text'],
                            ['label' => 'Giới tính', 'name' => 'gioi_tinh_thanh_vien', 'type' => 'select', 'options' => ['Nam', 'Nữ', 'Khác']],
                            ['label' => 'Số định danh cá nhân', 'name' => 'so_dinh_danh_thanh_vien', 'type' => 'text'],
                            ['label' => 'Quan hệ với chủ hộ', 'name' => 'quan_he_chu_ho_thanh_vien', 'type' => 'text']
                        ]
                    ],

                    [
                        'group' => 'Thông tin xác nhận tờ khai thông tin cư trú bản điện tử',
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo nhân khẩu'],
                        'fields' => [
                            [
                                'label' => 'Người kê khai là',
                                'name' => 'nguoi_ke_khai_la',
                                'type' => 'select',
                                'options' => ['Chủ hộ', 'Chủ sở hữu chỗ ở hợp pháp', 'Cha/Mẹ/Người giám hộ']
                            ]
                        ]
                    ],

                    [
                        'group' => 'Thông tin nhận thông báo tình trạng hồ sơ, kết quả giải quyết hồ sơ',
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo nhân khẩu'],
                        'fields' => [
                            [
                                'label' => 'Hình thức nhận kết quả',
                                'name' => 'hinh_thuc_nhan',
                                'type' => 'select',
                                'options' => ['Trực tiếp tại cơ quan', 'Qua bưu điện', 'Qua cổng dịch vụ công']
                            ]
                        ]
                    ],

                    [
                        'group' => 'Thông tin lệ phí',
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo nhân khẩu'],
                        'fields' => [
                            [
                                'label' => 'Lệ phí',
                                'name' => 'loai_le_phi',
                                'type' => 'radio',
                                'options' => ['Có phí', 'Miễn phí', 'Không phải nộp lệ phí']
                            ],
                            [
                                'label' => 'Lý do miễn lệ phí',
                                'name' => 'ly_do_mien_phi',
                                'type' => 'text',
                                'show_if' => ['loai_le_phi' => 'Miễn phí']
                            ]
                        ]
                    ],

                    // =============================
                    // 2️⃣ TRƯỜNG HỢP: DANH SÁCH
                    // =============================

                    [
                        'group' => 'Thông tin đề nghị đăng ký tạm trú',
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo danh sách'],
                        'fields' => [
                            ['label' => 'Địa chỉ đề nghị đăng ký', 'name' => 'dia_chi', 'type' => 'text'],
                            ['label' => 'Họ và tên người đại diện theo pháp luật', 'name' => 'nguoi_dai_dien_ho_ten', 'type' => 'text'],
                            ['label' => 'Ngày tháng năm sinh', 'name' => 'nguoi_dai_dien_ngay_sinh', 'type' => 'date'],
                            ['label' => 'Số định danh cá nhân', 'name' => 'nguoi_dai_dien_dinh_danh', 'type' => 'text'],
                            ['label' => 'Giới tính', 'name' => 'nguoi_dai_dien_gioi_tinh', 'type' => 'select', 'options' => ['Nam', 'Nữ']],
                            ['label' => 'Số điện thoại', 'name' => 'nguoi_dai_dien_sdt', 'type' => 'text'],
                            ['label' => 'Email', 'name' => 'nguoi_dai_dien_email', 'type' => 'email'],
                            ['label' => 'Nơi thường trú', 'name' => 'nguoi_dai_dien_thuong_tru', 'type' => 'text'],
                            ['label' => 'Nơi ở hiện tại', 'name' => 'nguoi_dai_dien_noi_o', 'type' => 'text'],
                            ['label' => 'Nội dung đề nghị', 'name' => 'noi_dung_de_nghi', 'type' => 'textarea'],
                            ['label' => 'Ý kiến người đề nghị', 'name' => 'y_kien', 'type' => 'textarea'],
                            ['label' => 'Thời hạn tạm trú đề nghị đến ngày', 'name' => 'thoi_han_den_ngay', 'type' => 'date']
                        ]
                    ],

                    [
                        'group' => 'Danh sách công dân đăng ký tạm trú',
                        'repeatable' => true,
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo danh sách'],
                        'fields' => [
                            ['label' => 'Họ và tên', 'name' => 'ho_ten_cong_dan', 'type' => 'text'],
                            ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_cong_dan', 'type' => 'date'],
                            ['label' => 'Giới tính', 'name' => 'gioi_tinh_cong_dan', 'type' => 'select', 'options' => ['Nam', 'Nữ']],
                            ['label' => 'Số định danh cá nhân', 'name' => 'so_dinh_danh_cong_dan', 'type' => 'text'],
                            ['label' => 'Thời hạn đề nghị tạm trú', 'name' => 'thoi_han_cong_dan', 'type' => 'date']
                        ]
                    ],

                    [
                        'group' => 'Thông tin xác nhận tờ khai thông tin cư trú bản điện tử',
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo danh sách'],
                        'fields' => [
                            [
                                'label' => 'Người kê khai là',
                                'name' => 'nguoi_ke_khai_la',
                                'type' => 'select',
                                'options' => ['Chủ hộ', 'Chủ sở hữu chỗ ở hợp pháp', 'Cha/Mẹ/Người giám hộ']
                            ]
                        ]
                    ],

                    [
                        'group' => 'Thông tin nhận thông báo tình trạng hồ sơ, kết quả giải quyết hồ sơ',
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo danh sách'],
                        'fields' => [
                            [
                                'label' => 'Hình thức nhận kết quả',
                                'name' => 'hinh_thuc_nhan_kq',
                                'type' => 'select',
                                'options' => ['Trực tiếp', 'Qua bưu điện', 'Qua cổng dịch vụ công']
                            ]
                        ]
                    ],

                    [
                        'group' => 'Thông tin lệ phí',
                        'show_if' => ['truong_hop' => 'Đăng ký tạm trú theo danh sách'],
                        'fields' => [
                            [
                                'label' => 'Lệ phí',
                                'name' => 'loai_le_phi',
                                'type' => 'radio',
                                'options' => ['Có phí', 'Miễn phí', 'Không phải nộp lệ phí']
                            ],
                            [
                                'label' => 'Lý do miễn lệ phí',
                                'name' => 'ly_do_mien_phi',
                                'type' => 'text',
                                'show_if' => ['loai_le_phi' => 'Miễn phí']
                            ]
                        ]
                    ]
                ])
            ],
                [
                'maForm' => 2,
                'maTTHC' => 3, // Ví dụ: mã thủ tục Đăng ký kết hôn
                'cauHinhForm' => json_encode([

                    // ==================================================
                    // 🧾 FORM 1: THÔNG TIN NGƯỜI NỘP
                    // ==================================================
                    [
                        'group' => 'Thông tin người nộp hồ sơ',
                        'fields' => [
                            ['label' => 'Họ và tên', 'name' => 'ho_ten', 'type' => 'text'],
                            ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date'],
                            ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text'],
                            ['label' => 'Email', 'name' => 'email', 'type' => 'email'],
                            [
                                'label' => 'Giấy tờ tùy thân',
                                'name' => 'loai_giay_to',
                                'type' => 'select',
                                'options' => [
                                    'Chứng minh nhân dân',
                                    'Căn cước công dân',
                                    'Hộ chiếu',
                                    'Thẻ căn cước'
                                ]
                            ],
                            ['label' => 'Số giấy tờ', 'name' => 'so_giay_to', 'type' => 'text'],
                            ['label' => 'Nơi cấp giấy tờ', 'name' => 'noi_cap_giay_to', 'type' => 'text'],
                            ['label' => 'Quốc gia', 'name' => 'quoc_gia', 'type' => 'text'],
                            ['label' => 'Tỉnh/Thành phố', 'name' => 'tinh_thanh', 'type' => 'text'],
                            ['label' => 'Phường/Xã', 'name' => 'phuong_xa', 'type' => 'text'],
                            ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text'],
                            ['label' => 'Tên cơ quan / doanh nghiệp / hộ kinh doanh', 'name' => 'ten_co_quan', 'type' => 'text'],
                            ['label' => 'Mã số thuế / Mã doanh nghiệp', 'name' => 'ma_so_thue', 'type' => 'text']
                        ]
                    ],

                    // ==================================================
                    // 💍 FORM 2: THÔNG TIN KẾT HÔN
                    // ==================================================
                    [
                        'group' => 'Thông tin kết hôn',
                        'fields' => [
                            [
                                'label' => 'Loại đăng ký',
                                'name' => 'loai_dang_ky',
                                'type' => 'radio',
                                'options' => [
                                    'Đăng ký mới',
                                    'Đăng ký lại',
                                    'Ghi sổ việc đã kết hôn ở nước ngoài'
                                ]
                            ],
                            [
                                'label' => 'Loại hồ sơ liên thông',
                                'name' => 'loai_ho_so_lien_thong',
                                'type' => 'select',
                                'options' => [
                                    'Loại hồ sơ liên thông',
                                    'Không phải'
                                ]
                            ],
                            ['label' => 'Số lượng bản sao đề nghị cấp', 'name' => 'so_luong_ban_sao', 'type' => 'number']
                        ]
                    ],

                    // ==================================================
                    // 👰 FORM 3: THÔNG TIN BÊN NỮ
                    // ==================================================
                    [
                        'group' => 'Thông tin bên nữ',
                        'fields' => [
                            ['label' => 'Họ', 'name' => 'ho_nu', 'type' => 'text'],
                            ['label' => 'Đệm', 'name' => 'dem_nu', 'type' => 'text'],
                            ['label' => 'Tên', 'name' => 'ten_nu', 'type' => 'text'],
                            ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nu', 'type' => 'number'],
                            ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nu', 'type' => 'number'],
                            ['label' => 'Năm sinh', 'name' => 'nam_sinh_nu', 'type' => 'number'],
                            [
                                'label' => 'Loại cư trú',
                                'name' => 'loai_cu_tru_nu',
                                'type' => 'radio',
                                'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại']
                            ],
                            ['label' => 'Quốc gia cư trú', 'name' => 'quoc_gia_nu', 'type' => 'text'],
                            ['label' => 'Tỉnh/Thành phố', 'name' => 'tinh_thanh_nu', 'type' => 'text'],
                            ['label' => 'Phường/Xã', 'name' => 'phuong_xa_nu', 'type' => 'text'],
                            ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nu', 'type' => 'text'],
                            ['label' => 'Dân tộc', 'name' => 'dan_toc_nu', 'type' => 'text'],
                            ['label' => 'Quốc tịch', 'name' => 'quoc_tich_nu', 'type' => 'text'],
                            [
                                'label' => 'Loại giấy tờ tùy thân',
                                'name' => 'loai_giay_to_nu',
                                'type' => 'select',
                                'options' => [
                                    'Chứng minh nhân dân',
                                    'Hộ chiếu',
                                    'Thẻ căn cước',
                                    'Căn cước công dân',
                                    'Giấy chứng minh quân đội nhân dân',
                                    'Giấy chứng minh sĩ quan quân đội'
                                ]
                            ],
                            ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nu', 'type' => 'text'],
                            ['label' => 'Ngày cấp', 'name' => 'ngay_cap_nu', 'type' => 'date'],
                            ['label' => 'Nơi cấp', 'name' => 'noi_cap_nu', 'type' => 'text'],
                            ['label' => 'Số lần kết hôn của người vợ', 'name' => 'so_lan_ket_hon_nu', 'type' => 'number'],
                            ['label' => 'Số định danh cá nhân', 'name' => 'so_dinh_danh_nu', 'type' => 'text'],
                            [
                                'label' => 'Loại tình trạng hôn nhân',
                                'name' => 'tinh_trang_hon_nhan_nu',
                                'type' => 'select',
                                'options' => [
                                    'Hiện tại chưa đăng ký kết hôn với ai',
                                    'Hiện tại đang có vợ/chồng',
                                    'Đã ly hôn, hiện tại chưa đăng ký kết hôn với ai',
                                    'Đã có vợ/chồng nhưng người kia đã mất, hiện tại chưa đăng ký kết hôn với ai',
                                    'Khác'
                                ]
                            ],
                            ['label' => 'Tình trạng hôn nhân', 'name' => 'tinh_trang_hon_nhan_khac_nu', 'type' => 'textarea']
                        ]
                    ],

                    // ==================================================
                    // 🤵 FORM 4: THÔNG TIN BÊN NAM
                    // ==================================================
                    [
                        'group' => 'Thông tin bên nam',
                        'fields' => [
                            ['label' => 'Họ', 'name' => 'ho_nam', 'type' => 'text'],
                            ['label' => 'Đệm', 'name' => 'dem_nam', 'type' => 'text'],
                            ['label' => 'Tên', 'name' => 'ten_nam', 'type' => 'text'],
                            ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nam', 'type' => 'number'],
                            ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nam', 'type' => 'number'],
                            ['label' => 'Năm sinh', 'name' => 'nam_sinh_nam', 'type' => 'number'],
                            [
                                'label' => 'Loại cư trú',
                                'name' => 'loai_cu_tru_nam',
                                'type' => 'radio',
                                'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại']
                            ],
                            ['label' => 'Quốc gia cư trú', 'name' => 'quoc_gia_nam', 'type' => 'text'],
                            ['label' => 'Tỉnh/Thành phố', 'name' => 'tinh_thanh_nam', 'type' => 'text'],
                            ['label' => 'Phường/Xã', 'name' => 'phuong_xa_nam', 'type' => 'text'],
                            ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nam', 'type' => 'text'],
                            ['label' => 'Dân tộc', 'name' => 'dan_toc_nam', 'type' => 'text'],
                            ['label' => 'Quốc tịch', 'name' => 'quoc_tich_nam', 'type' => 'text'],
                            [
                                'label' => 'Loại giấy tờ tùy thân',
                                'name' => 'loai_giay_to_nam',
                                'type' => 'select',
                                'options' => [
                                    'Chứng minh nhân dân',
                                    'Hộ chiếu',
                                    'Thẻ căn cước',
                                    'Căn cước công dân',
                                    'Giấy chứng minh quân đội nhân dân',
                                    'Giấy chứng minh sĩ quan quân đội'
                                ]
                            ],
                            ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nam', 'type' => 'text'],
                            ['label' => 'Ngày cấp', 'name' => 'ngay_cap_nam', 'type' => 'date'],
                            ['label' => 'Nơi cấp', 'name' => 'noi_cap_nam', 'type' => 'text'],
                            ['label' => 'Số lần kết hôn của người chồng', 'name' => 'so_lan_ket_hon_nam', 'type' => 'number'],
                            ['label' => 'Số định danh cá nhân', 'name' => 'so_dinh_danh_nam', 'type' => 'text'],
                            [
                                'label' => 'Loại tình trạng hôn nhân',
                                'name' => 'tinh_trang_hon_nhan_nam',
                                'type' => 'select',
                                'options' => [
                                    'Hiện tại chưa đăng ký kết hôn với ai',
                                    'Hiện tại đang có vợ/chồng',
                                    'Đã ly hôn, hiện tại chưa đăng ký kết hôn với ai',
                                    'Đã có vợ/chồng nhưng người kia đã mất, hiện tại chưa đăng ký kết hôn với ai',
                                    'Khác'
                                ]
                            ],
                            ['label' => 'Tình trạng hôn nhân', 'name' => 'tinh_trang_hon_nhan_khac_nam', 'type' => 'textarea']
                        ]
                    ]
                ])
            ]

        ]);
    }
}
