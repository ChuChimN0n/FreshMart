<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TaiKhoan extends Authenticatable
{
    protected $table = 'TaiKhoan';

    protected $primaryKey = 'maTK';

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
        if (! $this->vaiTro) {
            return false;
        }

        return $this->vaiTro->quyens->contains('tenQuyen', $permission);
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
