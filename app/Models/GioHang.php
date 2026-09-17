<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GioHang extends Model
{
    protected $table = 'GioHang';

    protected $primaryKey = 'maGioHang';

    public $timestamps = false;

    protected $fillable = [
        'maTK',
        'tongTien',
    ];

    protected function casts(): array
    {
        return [
            'tongTien' => 'decimal:2',
        ];
    }

    public function taiKhoan(): BelongsTo
    {
        return $this->belongsTo(TaiKhoan::class, 'maTK');
    }

    public function chiTietGioHangs(): HasMany
    {
        return $this->hasMany(ChiTietGioHang::class, 'maGioHang');
    }

    public function tinhTongTien(): float
    {
        $tong = $this->chiTietGioHangs()->sum('thanhTien');
        $this->update(['tongTien' => $tong]);

        return $tong;
    }

    public function applyCurrentPrices(): bool
    {
        $this->loadMissing('chiTietGioHangs.sanPham');
        $pricesChanged = false;

        foreach ($this->chiTietGioHangs as $detail) {
            if ($detail->sanPham) {
                $pricesChanged = $pricesChanged || $detail->donGia != $detail->sanPham->giaBan;
                $detail->donGia = $detail->sanPham->giaBan;
                $detail->thanhTien = $detail->soLuong * $detail->donGia;
            }
        }

        $this->tongTien = $this->chiTietGioHangs->sum('thanhTien');

        return $pricesChanged;
    }
}
