<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quyen extends Model
{
    public const SUPPLIERS = 'Quản lý nhà cung cấp';

    public const ACCOUNTS = 'Quản lý tài khoản người dùng';

    public const ROLES = 'Phân quyền';

    public const PRODUCT_REPORT = 'Thống kê sản phẩm';

    public const ORDER_REPORT = 'Thống kê đơn hàng';

    public const REVENUE_REPORT = 'Thống kê doanh thu';

    public const PRODUCTS = 'Quản lý sản phẩm';

    public const CATEGORIES = 'Quản lý danh mục';

    public const ORDERS = 'Quản lý đơn hàng';

    public const REVIEWS = 'Quản lý đánh giá';

    public const ADMIN_PERMISSIONS = [self::SUPPLIERS, self::ACCOUNTS, self::ROLES, self::PRODUCT_REPORT, self::ORDER_REPORT, self::REVENUE_REPORT];

    public const STAFF_PERMISSIONS = [self::PRODUCTS, self::CATEGORIES, self::ORDERS, self::REVIEWS];

    public const CUSTOMER_PERMISSIONS = ['Quản lý giỏ hàng', 'Đặt hàng và theo dõi đơn hàng', 'Đánh giá sản phẩm'];

    public const MANAGEMENT_PERMISSIONS = [...self::ADMIN_PERMISSIONS, ...self::STAFF_PERMISSIONS];

    public const ROUTE_PERMISSIONS = [
        'giohang.*' => ['Quản lý giỏ hàng'],
        'checkout' => ['Đặt hàng và theo dõi đơn hàng'],
        'donhang.place' => ['Đặt hàng và theo dõi đơn hàng'],
        'donhang.index' => ['Đặt hàng và theo dõi đơn hàng'],
        'donhang.detail' => ['Đặt hàng và theo dõi đơn hàng'],
        'donhang.cancel' => ['Đặt hàng và theo dõi đơn hàng'],
        'danhgia.store' => ['Đánh giá sản phẩm'],
        'admin.dashboard' => self::ADMIN_PERMISSIONS,
        'admin.suggest.code' => self::ADMIN_PERMISSIONS,
        'admin.nhacungcap.*' => [self::SUPPLIERS],
        'admin.taikhoan.*' => [self::ACCOUNTS],
        'admin.vaitro.*' => [self::ROLES],
        'admin.baocao.index' => [self::PRODUCT_REPORT, self::ORDER_REPORT, self::REVENUE_REPORT],
        'admin.baocao.sanpham' => [self::PRODUCT_REPORT],
        'admin.baocao.donhang' => [self::ORDER_REPORT],
        'admin.baocao.doanhthu' => [self::REVENUE_REPORT],
        'staff.dashboard' => self::STAFF_PERMISSIONS,
        'staff.suggest.code' => self::STAFF_PERMISSIONS,
        'staff.sanpham.*' => [self::PRODUCTS],
        'staff.danhmuc.*' => [self::CATEGORIES],
        'staff.donhang.*' => [self::ORDERS],
        'staff.danhgia.*' => [self::REVIEWS],
    ];

    protected $table = 'Quyen';

    protected $primaryKey = 'maQuyen';

    public $timestamps = false;

    protected $fillable = [
        'tenQuyen',
    ];

    public function vaiTros(): BelongsToMany
    {
        return $this->belongsToMany(VaiTro::class, 'VaiTroQuyen', 'maQuyen', 'maVT');
    }
}
