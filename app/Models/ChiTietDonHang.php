<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietDonHang extends Model
{
    protected $table = 'ChiTietDonHang';

    protected $primaryKey = 'maCTDH';

    public $timestamps = false;

    protected $fillable = [
        'maDH',
        'maSP',
        'soLuong',
        'donGia',
        'thanhTien',
    ];

    protected function casts(): array
    {
        return [
            'soLuong' => 'integer',
            'donGia' => 'decimal:2',
            'thanhTien' => 'decimal:2',
        ];
    }

    public function donHang(): BelongsTo
    {
        return $this->belongsTo(DonHang::class, 'maDH');
    }

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'maSP');
    }
}
