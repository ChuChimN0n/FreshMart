<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class TaiKhoan extends Authenticatable
{
    protected $table = 'TaiKhoan';

    protected $primaryKey = 'maTK';

    protected $authPasswordName = 'matKhau';

    public $timestamps = false;

    protected $fillable = [
        'maVT',
        'hoTen',
        'tenDangNhap',
        'matKhau',
        'email',
        'soDienThoai',
        'diaChi',
        'trangThai',
    ];

    protected $hidden = [
        'matKhau',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'matKhau' => 'hashed',
        ];
    }

    public function vaiTro(): BelongsTo
    {
        return $this->belongsTo(VaiTro::class, 'maVT');
    }

    public function gioHang(): HasOne
    {
        return $this->hasOne(GioHang::class, 'maTK');
    }

    public function donHangs(): HasMany
    {
        return $this->hasMany(DonHang::class, 'maTK');
    }

    public function danhGias(): HasMany
    {
        return $this->hasMany(DanhGia::class, 'maTK');
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($this->isAdmin() && in_array($permission, [...Quyen::ADMIN_PERMISSIONS, ...Quyen::STAFF_PERMISSIONS], true)) {
            return true;
        }

        $this->loadMissing('vaiTro.quyens');
        if (! $this->vaiTro) {
            return false;
        }

        return $this->vaiTro->quyens->contains('tenQuyen', $permission);
    }

    public function canAccessRoute(string $route): bool
    {
        if ($route === 'admin.dashboard') {
            return $this->isActive() && $this->isAdmin();
        }

        if ($route === 'staff.dashboard') {
            foreach (Quyen::STAFF_PERMISSIONS as $permission) {
                if ($this->hasPermission($permission)) {
                    return true;
                }
            }

            return false;
        }

        foreach (Quyen::ROUTE_PERMISSIONS as $pattern => $permissions) {
            if (Str::is($pattern, $route)) {
                foreach ($permissions as $permission) {
                    if ($this->hasPermission($permission)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function dashboardRoute(): string
    {
        foreach (config('navigation.management', []) as $item) {
            if ($this->canAccessRoute($item['route'])) {
                return $item['route'];
            }
        }

        return 'home';
    }

    public function isAdmin(): bool
    {
        return $this->maVT === VaiTro::ADMIN_ID;
    }

    public function isStaff(): bool
    {
        return $this->maVT === VaiTro::STAFF_ID;
    }

    public function isCustomer(): bool
    {
        return $this->maVT === VaiTro::KHACH_HANG_ID;
    }

    public function isActive(): bool
    {
        return $this->trangThai === 'HOAT_DONG';
    }
}
