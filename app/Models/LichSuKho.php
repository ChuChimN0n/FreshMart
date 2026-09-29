<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichSuKho extends Model
{
    const NHAP_HANG = 'NHAP_HANG';

    const BAN_HANG = 'BAN_HANG';

    const HOAN_DON = 'HOAN_DON';

    protected $table = 'LichSuKho';

    protected $primaryKey = 'maLSK';

    public $timestamps = false;

    protected $fillable = [
        'maSP',
        'loaiBienDong',
        'soLuong',
        'tonTruoc',
        'tonSau',
        'maPN',
        'maDH',
        'maTK',
        'thoiGian',
        'ghiChu',
    ];

    protected function casts(): array
    {
        return [
            'soLuong' => 'integer',
            'tonTruoc' => 'integer',
            'tonSau' => 'integer',
            'thoiGian' => 'datetime',
        ];
    }

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'maSP');
    }

    public function phieuNhap(): BelongsTo
    {
        return $this->belongsTo(PhieuNhap::class, 'maPN');
    }

    public function donHang(): BelongsTo
    {
        return $this->belongsTo(DonHang::class, 'maDH');
    }

    public function taiKhoan(): BelongsTo
    {
        return $this->belongsTo(TaiKhoan::class, 'maTK');
    }

    /**
     * Ghi 1 dòng biến động kho. Gọi trong transaction của nghiệp vụ.
     */
    public static function ghiNhan(
        int $maSP,
        string $loaiBienDong,
        int $soLuong,
        int $tonTruoc,
        int $tonSau,
        ?int $maPN = null,
        ?int $maDH = null,
        ?int $maTK = null,
        ?string $ghiChu = null
    ): self {
        return self::create([
            'maSP' => $maSP,
            'loaiBienDong' => $loaiBienDong,
            'soLuong' => $soLuong,
            'tonTruoc' => $tonTruoc,
            'tonSau' => $tonSau,
            'maPN' => $maPN,
            'maDH' => $maDH,
            'maTK' => $maTK,
            'thoiGian' => now(),
            'ghiChu' => $ghiChu,
        ]);
    }
}
