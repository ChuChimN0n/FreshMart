<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SanPham extends Model
{
    const DANG_BAN = 'DANG_BAN';

    const NGUNG_BAN = 'NGUNG_BAN';

    protected $table = 'SanPham';

    protected $primaryKey = 'maSP';

    public $timestamps = false;

    protected $fillable = [
        'maNCC',
        'maDM',
        'tenSP',
        'hinhAnh',
        'donVi',
        'giaBan',
        'moTa',
        'soLuong',
        'trangThai',
    ];

    protected function casts(): array
    {
        return [
            'giaBan' => 'decimal:2',
            'soLuong' => 'integer',
        ];
    }

    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'maNCC');
    }

    public function danhMuc(): BelongsTo
    {
        return $this->belongsTo(DanhMuc::class, 'maDM');
    }

    public function chiTietGioHangs(): HasMany
    {
        return $this->hasMany(ChiTietGioHang::class, 'maSP');
    }

    public function chiTietDonHangs(): HasMany
    {
        return $this->hasMany(ChiTietDonHang::class, 'maSP');
    }

    public function danhGias(): HasMany
    {
        return $this->hasMany(DanhGia::class, 'maSP');
    }

    public function isAvailable(): bool
    {
        return $this->trangThai === self::DANG_BAN && $this->soLuong > 0;
    }
}
