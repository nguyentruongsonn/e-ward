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
            ],


                //========================================Cấp bản sao trích lục hộ tịch, bản sao giấy khai sinh
                [

                'maTTHC' => 1, //
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
                    // 💍 FORM 2: THÔNG TIN NGƯỜI YÊU CẦU
                    // ==================================================
                    [
                        'group' => 'Thông tin người yêu cầu',
                        'fields' => [
                            [
                                'type' =>'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' =>
                                    [
                                        'label' => 'Họ',
                                        'name' => 'ho_nguoi_yeu_cau',
                                        'type' => 'text',
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Chữ đệm',
                                        'name' => 'dem_nguoi_yeu_cau',
                                        'type' =>  'text',
                                        'col' => 4
                                    ],
                                    [
                                        'label' => 'Tên',
                                        'name' =>'ten_nguoi_yeu_cau',
                                        'type' =>'text',
                                        'col' => 4
                                    ]
                            ],

                            [
                                'type'=>'row',
                                'title' => "Ngày tháng năm sinh",
                                'columns' =>
                                    [
                                        'label' => 'Ngày',
                                        'name' => 'ngay_nguoi_yeu_cau',
                                        'type' => 'number',
                                        'min'=>1,
                                        'max'=>31,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Tháng',
                                        'name' => 'thang_nguoi_yeu_cau',
                                        'type' => 'number',
                                        'min'=>1,
                                        'max'=>12,
                                        'col' => 4,
                                    ],
                                    [
                                        'label'=>'Năm',
                                        'name'=> 'nam_nguoi_yeu_cau',
                                        'type' => 'number',
                                        'col' =>4
                                    ]
                            ],

                            [
                                'type' => 'row',
                                'columns' =>
                                [
                                    'label' => 'Loại cư trú',
                                    'name' => 'loai_cu_tru_nguoi_yeu_cau',
                                    'col' => 4,
                                    'type' => 'select',
                                    'options' =>
                                    [
                                        'Thường trú',
                                        'Tạm trú',
                                        'Nơi ở hiện tại'
                                    ]
                                ]
                            ],

                            [
                                'type' => 'row',
                                'title'=> 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên','Venezuela','Myanmar','Tây Ban Nha','Ả Rập Xê Úp','Ả Rập','Campuchia','Indonesia','Bhutan','Hungary','Australia','Lào','Iran','Pakistan'
                                        ]
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nguoi_yeu_cai',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nu', 'type' => 'text', 'col' => 3,],
                                ]
                            ],
                            //
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_tuy_than_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước công dân','Giấy chứng minh Quân đội nhân dân','Giấy chứng minh Sỹ quan quân đội','Giấy chứng minh Công an nhân dân','Giấy tờ khác','Giấy khai sinh','Giấy chứng sinh','Thẻ căn cước','Giấy chứng nhân căn cước','3'
                                        ]
                                    ],
                                    [
                                        'label' => 'Số giấy tờ',
                                        'name' => 'so_giay_to_nguoi_yeu_cau',
                                        'type' => 'text',
                                        'col' => 3,
                                    ],
                                    [
                                        'label' => 'Ngày cấp',
                                        'name' => 'ngay_cap_nguoi_yeu_cau',
                                        'type' => 'date',
                                        'col' => 3,
                                    ],
                                    ['label' => 'Nơi cấp giấy tờ', 'name' => 'noi_cap_giay_to_nguoi_yeu_cau', 'type' => 'text', 'col' => 3,],
                                ]
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quan hệ với người đề nghị',
                                        'name' => 'quan_he_voi_nguoi_de_nghi',
                                        'type' => 'text',
                                        'col' => 6,
                                    ],
                                    [
                                        'label' => 'Loại hộ tịch',
                                        'name' => 'loai_ho_tich_nguoi_yeu_cau',
                                        'type' => 'text',
                                        'col' => 6,
                                        'disable' => true,
                                        'value' => 'BS'
                                    ],

                                ]
                            ],
                        ]
                    ],

                    // ==================================================
                    // 👰 FORM 3: THÔNG TIN NGƯỜI ĐƯỢC CẤP BẢN SAO
                    // ==================================================
                    [
                        'group' => 'Thông tin người được cấp bản sao Giấy khai sinh, bản sao Trích lục hộ tịch',
                        'fields' => [
                            [
                                'type' =>'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' =>
                                    [
                                        'label' => 'Họ',
                                        'name' => 'ho_nguoi_duoc_cap',
                                        'type' => 'text',
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Chữ đệm',
                                        'name' => 'dem_nguoi_duoc_cap',
                                        'type' =>  'text',
                                        'col' => 4
                                    ],
                                    [
                                        'label' => 'Tên',
                                        'name' =>'ten_nguoi_duoc_cap',
                                        'type' =>'text',
                                        'col' => 4
                                    ]
                            ],

                            [
                                'type'=>'row',
                                'title' => "Ngày tháng năm sinh",
                                'columns' =>
                                    [
                                        'label' => 'Ngày',
                                        'name' => 'ngay_nguoi_duoc_cap',
                                        'type' => 'number',
                                        'min'=>1,
                                        'max'=>31,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Tháng',
                                        'name' => 'thang_nguoi_duoc_cap',
                                        'type' => 'number',
                                        'min'=>1,
                                        'max'=>12,
                                        'col' => 4,
                                    ],
                                    [
                                        'label'=>'Năm',
                                        'name'=> 'nam_nguoi_duoc_cap',
                                        'type' => 'number',
                                        'col' =>4
                                    ]
                            ],

                            [
                                'type' => 'row',
                                'columns' =>
                                [
                                    'label' =>'Giới tính',
                                    'name' => 'gioi_tinh_nguoi_duoc_cap',
                                    'type' =>'select',
                                    'col' => 4,
                                    'options' =>[
                                        'Nam', 'Nữ'
                                    ]
                                ],
                                [
                                    'label' => 'Dân tộc',
                                    'name' => 'dan_toc_nguoi_duoc_cap',
                                    'type' =>'select',
                                    'col' => 4,
                                    'options' => [
                                            'Kinh','Khơ me','Hà Nhì','Giẻ Triêng','Hơ mông','Ê Đê','Ba Na','La Chí','Cờ Ho','Brâu','Xơ Đăng','Thổ','Thái','Tà Ôi','Hoa','Sán Dìu','Pu Péo'
                                    ]
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' =>
                                [
                                    'label' => 'Loại cư trú',
                                    'name' => 'loai_cu_tru_nguoi_duoc_cap',
                                    'col' => 4,
                                    'type' => 'select',
                                    'options' =>
                                    [
                                        'Thường trú',
                                        'Tạm trú',
                                        'Nơi ở hiện tại'
                                    ]
                                ]
                            ],

                            [
                                'type' => 'row',
                                'title'=> 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên','Venezuela','Myanmar','Tây Ban Nha','Ả Rập Xê Úp','Ả Rập','Campuchia','Indonesia','Bhutan','Hungary','Australia','Lào','Iran','Pakistan'
                                        ]
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh','Tỉnh Đồng Nai'
                                        ]
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nguoi_duoc_cap', 'type' => 'text', 'col' => 3,],
                                ]
                            ],
                            //
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_tuy_than_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước công dân','Giấy chứng minh Quân đội nhân dân','Giấy chứng minh Sỹ quan quân đội','Giấy chứng minh Công an nhân dân','Giấy tờ khác','Giấy khai sinh','Giấy chứng sinh','Thẻ căn cước','Giấy chứng nhân căn cước','3'
                                        ]
                                    ],
                                    [
                                        'label' => 'Số giấy tờ',
                                        'name' => 'so_giay_to_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                    ],
                                    [
                                        'label' => 'Ngày cấp',
                                        'name' => 'ngay_cap_nguoi_duoc_cap',
                                        'type' => 'date',
                                        'col' => 3,
                                    ],
                                    ['label' => 'Nơi cấp giấy tờ', 'name' => 'noi_cap_giay_to_nguoi_duoc_cap', 'type' => 'text', 'col' => 3,],
                                ]
                            ],

                        ]
                    ],
                    // ==================================================
                    // 🤵 FORM 4: NƠI ĐĂNG KÝ HỒ SƠ TRƯỚC ĐÂY
                    // ==================================================
                    [
                        'group' => 'Nơi đăng ký hồ sơ trước đây',

                        'fields' => [
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Nơi đăng ký hồ sơ trước đây', 'name' => 'noi_dang_ky_ho_so_truoc_day', 'type' => 'text', 'col' => 8],
                                ]
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Sổ đăng ký hộ tịch trước đây', 'name' => 'so_dang_ky_ho_tich_truoc_day', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Quyển sổ', 'name' => 'quyen_so', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Ngày đăng ký', 'name' => 'ngay_dang_ky', 'type' => 'date', 'col' => 4,],
                                ]
                            ],
                            [
                                'type' =>'row',
                                'columns' =>[
                                    ['label' => 'Thông tin khác','name'=>'thong_tin_khác','type'=>'text','col'=>12]
                                ]

                            ],

                            [
                                'label'=>'Ví dụ: Cung cấp thông tin về cha, mẹ để phục vụ việc cấp bản sao Giấy khai sinh; cung cấp thông tin về vợ, chồng để phục vụ việc cấp bản sao Trích lục kết hôn;...'
                            ]

                        ]
                    ],
                ])
            ],


            //===================== THỦ TỤC ĐĂNG KÝ KHAI SINH

        ]);
    }
}
