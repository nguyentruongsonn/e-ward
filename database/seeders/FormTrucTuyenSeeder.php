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
            ]
        ]);
    }
}
