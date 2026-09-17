<?php

return [
    'management' => [
        ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2'],
        ['route' => 'staff.dashboard', 'active' => 'staff.dashboard', 'label' => 'Dashboard nhân viên', 'icon' => 'bi-speedometer2'],
        ['route' => 'staff.sanpham.index', 'active' => 'staff.sanpham.*', 'label' => 'Sản phẩm', 'icon' => 'bi-box-seam'],
        ['route' => 'staff.danhmuc.index', 'active' => 'staff.danhmuc.*', 'label' => 'Danh mục', 'icon' => 'bi-tags'],
        ['route' => 'staff.donhang.index', 'active' => 'staff.donhang.*', 'label' => 'Đơn hàng', 'icon' => 'bi-receipt'],
        ['route' => 'staff.danhgia.index', 'active' => 'staff.danhgia.*', 'label' => 'Đánh giá', 'icon' => 'bi-star'],
        ['route' => 'admin.baocao.index', 'active' => 'admin.baocao.*', 'label' => 'Báo cáo', 'icon' => 'bi-bar-chart'],
        ['route' => 'admin.nhacungcap.index', 'active' => 'admin.nhacungcap.*', 'label' => 'Nhà cung cấp', 'icon' => 'bi-truck'],
        ['route' => 'admin.taikhoan.index', 'active' => 'admin.taikhoan.*', 'label' => 'Tài khoản', 'icon' => 'bi-people'],
        ['route' => 'admin.vaitro.index', 'active' => 'admin.vaitro.*', 'label' => 'Vai trò', 'icon' => 'bi-shield-lock'],
    ],
];
