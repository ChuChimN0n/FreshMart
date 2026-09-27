<?php

return [
    'profiles' => [
        'sanpham' => [
            'table' => 'SanPham',
            'column' => 'sku',
            'separator' => '-',
            'pad' => 4,
            // Prefix lấy từ tên danh mục: bỏ dấu, in hoa, lấy N ký tự đầu.
            // VD: "Rau củ quả" => RAU => RAU-0047
            'prefix' => ['type' => 'category', 'context_key' => 'maDM', 'length' => 3],
            'pattern' => '/^[A-Z0-9]{1,6}-\d{1,8}$/',
        ],
        'donhang' => [
            'table' => 'DonHang',
            'column' => 'maDon',
            'separator' => '-',
            'pad' => 4,
            // Prefix theo tháng: DH-YYYYMM => DH-202609-0001 (reset mỗi tháng).
            'prefix' => ['type' => 'dated', 'format' => 'DH', 'date_format' => 'Ym'],
            'pattern' => '/^DH-\d{6}-\d{4,}$/',
        ],
        'nhacungcap' => [
            'table' => 'NhaCungCap',
            'column' => 'codeNCC',
            'separator' => '-',
            'pad' => 4,
            // Dãy số toàn cục: NCC-0001, NCC-0002...
            'prefix' => ['type' => 'fixed', 'value' => 'NCC'],
            'pattern' => '/^NCC-\d{4,}$/',
        ],
    ],
];
