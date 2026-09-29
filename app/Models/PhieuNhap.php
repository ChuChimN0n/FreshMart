<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PhieuNhap extends Model
{
    const NHAP = 'NHAP';

    const DA_XAC_NHAN = 'DA_XAC_NHAN';

    const TRANG_THAI = [
        self::NHAP => 'Chờ xác nhận',
        self::DA_XAC_NHAN => 'Đã nhập kho',
    ];

    const TRANG_THAI_BADGE = [
        self::NHAP => 'bg-amber-100 text-amber-700',
        self::DA_XAC_NHAN => 'bg-green-100 text-green-700',
    ];

    protected $table = 'PhieuNhap';

    protected $primaryKey = 'maPN';

    public $timestamps = false;

    protected $fillable = [
        'maPhieu',
        'maNCC',
        'maNguoiTao',
        'ngayTao',
        'ngayXacNhan',
        'tongTien',
        'trangThai',
        'ghiChu',
    ];

    protected function casts(): array
    {
        return [
            'ngayTao' => 'datetime',
            'ngayXacNhan' => 'datetime',
            'tongTien' => 'decimal:2',
        ];
    }

    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'maNCC');
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(TaiKhoan::class, 'maNguoiTao');
    }

    public function chiTiets(): HasMany
    {
        return $this->hasMany(ChiTietPhieuNhap::class, 'maPN');
    }

    public function lichSuKhos(): HasMany
    {
        return $this->hasMany(LichSuKho::class, 'maPN');
    }

    public function isNhap(): bool
    {
        return $this->trangThai === self::NHAP;
    }

    public function getTrangThaiLabelAttribute(): string
    {
        return self::TRANG_THAI[$this->trangThai] ?? $this->trangThai;
    }

    public function getTrangThaiBadgeAttribute(): string
    {
        return self::TRANG_THAI_BADGE[$this->trangThai] ?? 'bg-gray-100 text-gray-700';
    }

    /**
     * Xác nhận phiếu: cộng tồn kho + ghi lịch sử NHAP_HANG.
     * Chỉ chuyển NHAP -> DA_XAC_NHAN, đúng 1 lần.
     */
    public function xacNhan(): bool
    {
        return DB::transaction(function (): bool {
            $phieu = self::whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            if ($phieu->trangThai !== self::NHAP) {
                return false;
            }

            foreach ($phieu->chiTiets()->orderBy('maSP')->lockForUpdate()->get() as $detail) {
                $sanPham = SanPham::whereKey($detail->maSP)->lockForUpdate()->first();

                if (! $sanPham) {
                    throw new \DomainException('Sản phẩm trong phiếu nhập không còn tồn tại!');
                }

                $tonTruoc = $sanPham->soLuong;
                $sanPham->increment('soLuong', $detail->soLuong);

                LichSuKho::ghiNhan(
                    maSP: $sanPham->maSP,
                    loaiBienDong: LichSuKho::NHAP_HANG,
                    soLuong: $detail->soLuong,
                    tonTruoc: $tonTruoc,
                    tonSau: $tonTruoc + $detail->soLuong,
                    maPN: $phieu->maPN,
                );
            }

            $phieu->update(['trangThai' => self::DA_XAC_NHAN, 'ngayXacNhan' => now()]);
            $this->setRawAttributes($phieu->getAttributes(), true);

            return true;
        }, 3);
    }
}
