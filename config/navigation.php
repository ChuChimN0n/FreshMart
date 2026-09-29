<?php

return [
    'management' => [
        ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'group' => 'overview'],
        ['route' => 'staff.dashboard', 'active' => 'staff.dashboard', 'label' => 'Dashboard nhân viên', 'icon' => 'bi-speedometer2', 'group' => 'overview'],
        ['route' => 'staff.sanpham.index', 'active' => 'staff.sanpham.*', 'label' => 'Sản phẩm', 'icon' => 'bi-box-seam', 'group' => 'sales'],
        ['route' => 'staff.nhaphang.index', 'active' => 'staff.nhaphang.*', 'label' => 'Nhập hàng', 'icon' => 'bi-box-arrow-in-down', 'group' => 'sales'],
        ['route' => 'staff.kho.index', 'active' => 'staff.kho.*', 'label' => 'Quản lý kho', 'icon' => 'bi-boxes', 'group' => 'sales'],
        ['route' => 'staff.danhmuc.index', 'active' => 'staff.danhmuc.*', 'label' => 'Danh mục', 'icon' => 'bi-tags', 'group' => 'sales'],
        ['route' => 'staff.donhang.index', 'active' => 'staff.donhang.*', 'label' => 'Đơn hàng', 'icon' => 'bi-receipt', 'group' => 'sales'],
        ['route' => 'staff.danhgia.index', 'active' => 'staff.danhgia.*', 'label' => 'Đánh giá', 'icon' => 'bi-star', 'group' => 'sales'],
        [
            'route' => 'admin.baocao.index',
            'active' => 'admin.baocao.*',
            'label' => 'Báo cáo',
            'icon' => 'bi-bar-chart',
            'group' => 'admin',
            'children' => [
                ['route' => 'admin.baocao.index', 'active' => 'admin.baocao.index', 'label' => 'Tổng quan'],
                ['route' => 'admin.baocao.sanpham', 'active' => 'admin.baocao.sanpham', 'label' => 'Theo sản phẩm'],
                ['route' => 'admin.baocao.donhang', 'active' => 'admin.baocao.donhang', 'label' => 'Theo đơn hàng'],
                ['route' => 'admin.baocao.doanhthu', 'active' => 'admin.baocao.doanhthu', 'label' => 'Theo doanh thu'],
                ['route' => 'admin.baocao.nhaphang', 'active' => 'admin.baocao.nhaphang', 'label' => 'Nhập hàng'],
                ['route' => 'admin.baocao.tonkho', 'active' => 'admin.baocao.tonkho', 'label' => 'Tồn kho'],
            ],
        ],
        ['route' => 'admin.nhacungcap.index', 'active' => 'admin.nhacungcap.*', 'label' => 'Nhà cung cấp', 'icon' => 'bi-truck', 'group' => 'admin'],
        ['route' => 'admin.taikhoan.index', 'active' => 'admin.taikhoan.*', 'label' => 'Tài khoản', 'icon' => 'bi-people', 'group' => 'admin'],
        ['route' => 'admin.vaitro.index', 'active' => 'admin.vaitro.*', 'label' => 'Vai trò', 'icon' => 'bi-shield-lock', 'group' => 'admin'],
    ],
];
