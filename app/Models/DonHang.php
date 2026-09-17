<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class DonHang extends Model
{
    protected $table = 'DonHang';

    protected $primaryKey = 'maDH';

    public $timestamps = false;

    protected $fillable = [
        'maTK',
        'ngayDat',
        'tenNguoiNhan',
        'soDienThoai',
        'diaChi',
        'tongTien',
        'trangThai',
    ];

    protected function casts(): array
    {
        return [
            'ngayDat' => 'datetime',
            'tongTien' => 'decimal:2',
        ];
    }

    public function taiKhoan(): BelongsTo
    {
        return $this->belongsTo(TaiKhoan::class, 'maTK');
    }

    public function chiTietDonHangs(): HasMany
    {
        return $this->hasMany(ChiTietDonHang::class, 'maDH');
    }

    const CHO_XAC_NHAN = 'CHO_XAC_NHAN';

    const DA_XAC_NHAN = 'DA_XAC_NHAN';

    const DANG_GIAO = 'DANG_GIAO';

    const HOAN_THANH = 'HOAN_THANH';

    const DA_HUY = 'DA_HUY';

    const TRANG_THAI = [
        self::CHO_XAC_NHAN => 'Chờ xác nhận',
        self::DA_XAC_NHAN => 'Đã xác nhận',
        self::DANG_GIAO => 'Đang giao',
        self::HOAN_THANH => 'Hoàn thành',
        self::DA_HUY => 'Đã hủy',
    ];

    const TRANG_THAI_BADGE = [
        self::CHO_XAC_NHAN => 'bg-yellow-100 text-yellow-700',
        self::DA_XAC_NHAN => 'bg-blue-100 text-blue-700',
        self::DANG_GIAO => 'bg-purple-100 text-purple-700',
        self::HOAN_THANH => 'bg-bhx-100 text-bhx-700',
        self::DA_HUY => 'bg-red-100 text-red-700',
    ];

    const VALID_TRANSITIONS = [
        self::CHO_XAC_NHAN => [self::DA_XAC_NHAN, self::DA_HUY],
        self::DA_XAC_NHAN => [self::DANG_GIAO, self::DA_HUY],
        self::DANG_GIAO => [self::HOAN_THANH],
    ];

    public function getTrangThaiLabelAttribute(): string
    {
        return self::TRANG_THAI[$this->trangThai] ?? $this->trangThai;
    }

    public function getTrangThaiBadgeAttribute(): string
    {
        return self::TRANG_THAI_BADGE[$this->trangThai] ?? 'bg-gray-100 text-gray-700';
    }

    public function canCancel(): bool
    {
        return in_array($this->trangThai, [self::CHO_XAC_NHAN]);
    }

    public function transitionTo(string $next, bool $customerCancellation = false): bool
    {
        return DB::transaction(function () use ($next, $customerCancellation): bool {
            $order = self::whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            if ($customerCancellation && (! $order->canCancel() || $next !== self::DA_HUY)) {
                return false;
            }

            if (! in_array($next, self::VALID_TRANSITIONS[$order->trangThai] ?? [], true)) {
                return false;
            }

            if ($next === self::DA_HUY) {
                foreach ($order->chiTietDonHangs()->orderBy('maSP')->get() as $detail) {
                    SanPham::whereKey($detail->maSP)->increment('soLuong', $detail->soLuong);
                }
            }

            $order->update(['trangThai' => $next]);
            $this->setRawAttributes($order->getAttributes(), true);

            return true;
        }, 3);
    }
}
