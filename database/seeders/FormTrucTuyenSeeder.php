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

                'maTTHC' => 2, // Đăng ký kết hôn
                'cauHinhForm' => json_encode([

                    // ==================================================
                    // 🧾 FORM 1: THÔNG TIN NGƯỜI NỘP
                    // ==================================================
                    [
                        'group' => 'Thông tin người nộp',
                        'fields' => [

                            // Họ tên - Ngày sinh - SĐT - Email
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ và tên ', 'name' => 'ho_ten', 'type' => 'text', 'col' => 3,],
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date', 'col' => 3,],
                                    ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 3,],
                                    ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'col' => 3],
                                ]
                            ],

                            // Giấy tờ tùy thân - Số giấy tờ - Ngày cấp - Nơi cấp
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Chứng minh nhân dân', 'Căn cước công dân', 'Hộ chiếu', 'Thẻ căn cước'
                                        ]
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to', 'type' => 'text', 'col' => 3,],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap', 'type' => 'date', 'col' => 3,],

                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to',
                                        'type' => 'select',
                                        'col' => 3,

                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định','Công an Tỉnh Bình Dương','Công an Tỉnh Bình Phước','Công an Tỉnh Bình Thuận','Công an Tỉnh Cà Mau','Công an TP.Cần Thơ','Công an Tỉnh Cao Bằng ','Công an TP. Hải Phòng','Công an Tỉnh Gia Lai','Công an Tỉnh Hà Nam','Công an Tỉnh Hòa Bình','Công an Tỉnh Hưng Yên ','Công an Tỉnh Hải Dương','Công an Tỉnh Hậu Giang','Công an Tỉnh Khánh Hòa','Công an Tỉnh Kiên Giang','Công an Tỉnh Kon Tum','Công an Tỉnh Lai Châu',''
                                        ]
                                    ],
                                ]
                            ],

                            // Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên','Venezuela','Myanmar','Tây Ban Nha','Ả Rập Xê Úp','Ả Rập','Campuchia','Indonesia','Bhutan','Hungary','Australia','Lào','Iran','Pakistan'
                                        ]
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                                                        [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text', 'col' => 3,],
                                ]
                            ],

                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Tên cơ quan / hộ kinh doanh', 'name' => 'ten_co_quan', 'type' => 'text', 'col' => 5],
                                    ['label' => 'Mã số thuế / Mã doanh nghiệp', 'name' => 'ma_so_thue', 'type' => 'text', 'col' => 4],
                                    [
                                        'label' => 'Hình thức nộp hồ sơ',
                                        'name' => 'hinh_thuc_nop',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => ['Trực tiếp', 'Trực tuyến', 'Dịch vụ bưu chính']
                                    ],
                                ]
                            ],
                             // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ  - Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên','Venezuela','Myanmar','Tây Ban Nha','Ả Rập Xê Úp','Ả Rập','Campuchia','Indonesia','Bhutan','Hungary','Australia','Lào','Iran','Pakistan'
                                        ]
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                                                        [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_co_quan', 'type' => 'text', 'col' => 3,],
                                ]
                            ]

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
                                'options' => ['Loại hồ sơ liên thông', 'Không phải']
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
                            [
                                'type' => 'row',
                                'title' => 'Họ và tên bên nữ',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nu', 'type' => 'text', 'col' => 4,],
                                    ['label' => 'Đệm', 'name' => 'dem_nu', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nu', 'type' => 'text', 'col' => 4,],
                                ]
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nu', 'type' => 'number', 'col' => 4],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nu', 'type' => 'number', 'col' => 4],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_nu', 'type' => 'number', 'col' => 4,],
                                ]
                            ],
                            [
                                'label' => 'Loại cư trú',
                                'name' => 'loai_cu_tru_nu',
                                'type' => 'radio',

                                'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại']
                            ],

                            [
                                'type' => 'row',
                                'title'=> 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên','Venezuela','Myanmar','Tây Ban Nha','Ả Rập Xê Úp','Ả Rập','Campuchia','Indonesia','Bhutan','Hungary','Australia','Lào','Iran','Pakistan'
                                        ]
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                                                        [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nu', 'type' => 'text', 'col' => 3,],
                                ]
                            ],

                            [
                                'type'=>'row',
                                'columns'=>[
                                    [
                                        'label'=>'Dân tộc',
                                        'name'=>'dan_toc_nu',
                                        'type'=>'select',
                                        'col'=>3,

                                        'options'=>[
                                            'Kinh','Khơ me','Hà Nhì','Giẻ Triêng','Hơ mông','Ê Đê','Ba Na','La Chí','Cờ Ho','Brâu','Xơ Đăng','Thổ','Thái','Tà Ôi','Hoa','Sán Dìu','Pu Péo'
                                        ]
                                    ],

                                    [
                                        'label'=>'Quốc tịch',
                                        'name'=>'quoc_tich_nu',
                                        'type'=>'select',
                                        'col'=>3,

                                        'options'=>[
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên','Venezuela','Myanmar','Tây Ban Nha','Ả Rập Xê Úp','Ả Rập','Campuchia','Indonesia','Bhutan','Hungary','Australia','Lào','Iran','Pakistan'
                                        ]
                                    ]
                                ]
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_nu',
                                        'type' => 'select',
                                        'col' => 3,

                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu','Thẻ thường trú', 'Thẻ căn cước','Giấy chứng minh quân đội nhân dân','Giấy chứng minh Sỹ quan quân đội',''
                                        ]
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nu', 'type' => 'text', 'col' => 3,],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap_nu', 'type' => 'date', 'col' => 3,],

                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to_nu',
                                        'type' => 'select',
                                        'col' => 3,

                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định','Công an Tỉnh Bình Dương','Công an Tỉnh Bình Phước','Công an Tỉnh Bình Thuận','Công an Tỉnh Cà Mau','Công an TP.Cần Thơ','Công an Tỉnh Cao Bằng ','Công an TP. Hải Phòng','Công an Tỉnh Gia Lai','Công an Tỉnh Hà Nam','Công an Tỉnh Hòa Bình','Công an Tỉnh Hưng Yên ','Công an Tỉnh Hải Dương','Công an Tỉnh Hậu Giang','Công an Tỉnh Khánh Hòa','Công an Tỉnh Kiên Giang','Công an Tỉnh Kon Tum','Công an Tỉnh Lai Châu',''
                                        ]
                                    ],
                                ]
                            ],

                            [
                                'type'=>'row',
                                'columns'=>[
                                    [
                                        'label'=>'Số lần kết hôn của người vợ',
                                        'name'=>'so_lan_ket_hon_cua_nguoi_vo',
                                        'type'=>'text',
                                        'col'=>3,
                                    ],
                                    [
                                        'label'=>'Số định danh cá nhân',
                                        'name'=>'so_dinh_danh_ca_nhan_vo',
                                        'type'=>'text',
                                        'col'=>3
                                    ]
                                ]
                            ],

                            [
                                'type'=>'row',
                                'columns'=>[
                                    [
                                        'label'=>'Loại trình trạng hôn nhân',
                                        'name'=>'loai_tinh_trang_hon_nhan_vo',
                                        'type'=>'select',
                                        'col'=>8,

                                        'options'=>[
                                            'Hiện tại chưa đăng ký kết hôn với ai','Hiện tại đang có vợ/chồng','Đã đăng ký kết hôn hoặc đã có vợ/chồng nhưng đã ly hôn; hiện tại chưa đăng ký kết hôn với ai','Đã đăng ký kết hôn hoặc đã có vợ/chồng nhưng vợ/chồng đã chết; hiện tại chưa đăng ký kết hôn với ai','Từ ngày...tháng...năm... đến ngày...tháng...năm... chưa đăng ký kết hôn với ai; hiện tại đang có vợ chồng','Khác - nếu không thuộc trường hợp trên'
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'type'=>'row',
                                'columns'=>[
                                    [
                                        'label'=>'Tình trang hôn nhân',
                                        'name'=>'tinh_trang_hon_nhan_vo',
                                        'type'=>'text',
                                        'col'=>8,

                                    ]
                                ]
                            ]
                        ]
                    ],

                    // ==================================================
                    // 🤵 FORM 4: THÔNG TIN BÊN NAM
                    // ==================================================
                    [
                        'group' => 'Thông tin bên nam',

                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ và tên bên nam',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nam', 'type' => 'text', 'col' => 4,],
                                    ['label' => 'Đệm', 'name' => 'dem_nam', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nam', 'type' => 'text', 'col' => 4,],
                                ]
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nam', 'type' => 'number', 'col' => 4],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nam', 'type' => 'number', 'col' => 4],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_nam', 'type' => 'number', 'col' => 4,],
                                ]
                            ],
                            [
                                'label' => 'Loại cư trú',
                                'name' => 'loai_cu_tru_nam',
                                'type' => 'radio',

                                'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại']
                            ],

                            [
                                'type' => 'row',
                                'title'=> 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nam',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên','Venezuela','Myanmar','Tây Ban Nha','Ả Rập Xê Úp','Ả Rập','Campuchia','Indonesia','Bhutan','Hungary','Australia','Lào','Iran','Pakistan'
                                        ]
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nam',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                                                        [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nam',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nam', 'type' => 'text', 'col' => 3,],
                                ]
                            ],

                            [
                                'type'=>'row',
                                'columns'=>[
                                    [
                                        'label'=>'Dân tộc',
                                        'name'=>'dan_toc_nam',
                                        'type'=>'select',
                                        'col'=>3,

                                        'options'=>[
                                            'Kinh','Khơ me','Hà Nhì','Giẻ Triêng','Hơ mông','Ê Đê','Ba Na','La Chí','Cờ Ho','Brâu','Xơ Đăng','Thổ','Thái','Tà Ôi','Hoa','Sán Dìu','Pu Péo'
                                        ]
                                    ],

                                    [
                                        'label'=>'Quốc tịch',
                                        'name'=>'quoc_tich_nam',
                                        'type'=>'select',
                                        'col'=>3,

                                        'options'=>[
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên','Venezuela','Myanmar','Tây Ban Nha','Ả Rập Xê Úp','Ả Rập','Campuchia','Indonesia','Bhutan','Hungary','Australia','Lào','Iran','Pakistan'
                                        ]
                                    ]
                                ]
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_nam',
                                        'type' => 'select',
                                        'col' => 3,

                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu','Thẻ thường trú', 'Thẻ căn cước','Giấy chứng minh quân đội nhân dân','Giấy chứng minh Sỹ quan quân đội',''
                                        ]
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nam', 'type' => 'text', 'col' => 3,],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap_nam', 'type' => 'date', 'col' => 3,],

                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to_nam',
                                        'type' => 'select',
                                        'col' => 3,

                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định','Công an Tỉnh Bình Dương','Công an Tỉnh Bình Phước','Công an Tỉnh Bình Thuận','Công an Tỉnh Cà Mau','Công an TP.Cần Thơ','Công an Tỉnh Cao Bằng ','Công an TP. Hải Phòng','Công an Tỉnh Gia Lai','Công an Tỉnh Hà Nam','Công an Tỉnh Hòa Bình','Công an Tỉnh Hưng Yên ','Công an Tỉnh Hải Dương','Công an Tỉnh Hậu Giang','Công an Tỉnh Khánh Hòa','Công an Tỉnh Kiên Giang','Công an Tỉnh Kon Tum','Công an Tỉnh Lai Châu',''
                                        ]
                                    ],
                                ]
                            ],

                            [
                                'type'=>'row',
                                'columns'=>[
                                    [
                                        'label'=>'Số lần kết hôn của người chồng',
                                        'name'=>'so_lan_ket_hon_cua_nguoi_chong',
                                        'type'=>'text',
                                        'col'=>3,
                                    ],
                                    [
                                        'label'=>'Số định danh cá nhân',
                                        'name'=>'so_dinh_danh_ca_nhan_chồng',
                                        'type'=>'text',
                                        'col'=>3
                                    ]
                                ]
                            ],

                            [
                                'type'=>'row',
                                'columns'=>[
                                    [
                                        'label'=>'Loại trình trạng hôn nhân',
                                        'name'=>'loai_tinh_trang_hon_nhan_chong',
                                        'type'=>'select',
                                        'col'=>8,

                                        'options'=>[
                                            'Hiện tại chưa đăng ký kết hôn với ai','Hiện tại đang có vợ/chồng','Đã đăng ký kết hôn hoặc đã có vợ/chồng nhưng đã ly hôn; hiện tại chưa đăng ký kết hôn với ai','Đã đăng ký kết hôn hoặc đã có vợ/chồng nhưng vợ/chồng đã chết; hiện tại chưa đăng ký kết hôn với ai','Từ ngày...tháng...năm... đến ngày...tháng...năm... chưa đăng ký kết hôn với ai; hiện tại đang có vợ chồng','Khác - nếu không thuộc trường hợp trên'
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'type'=>'row',
                                'columns'=>[
                                    [
                                        'label'=>'Tình trang hôn nhân',
                                        'name'=>'tinh_trang_hon_nhan_chong',
                                        'type'=>'text',
                                        'col'=>8,

                                    ]
                                ]
                            ]
                        ]
                    ],
                ])
            ]

        ]);
    }
}
