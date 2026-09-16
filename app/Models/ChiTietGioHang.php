<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietGioHang extends Model
{
    protected $table = 'ChiTietGioHang';

    protected $primaryKey = 'maCTGH';

    public $timestamps = false;

    protected $fillable = [
        'maGioHang',
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

    public function gioHang(): BelongsTo
    {
        return $this->belongsTo(GioHang::class, 'maGioHang');
    }

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'maSP');
    }
}
