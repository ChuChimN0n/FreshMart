<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietPhieuNhap extends Model
{
    protected $table = 'ChiTietPhieuNhap';

    protected $primaryKey = 'maCTPN';

    public $timestamps = false;

    protected $fillable = [
        'maPN',
        'maSP',
        'soLuong',
        'giaNhap',
        'thanhTien',
    ];

    protected function casts(): array
    {
        return [
            'soLuong' => 'integer',
            'giaNhap' => 'decimal:2',
            'thanhTien' => 'decimal:2',
        ];
    }

    public function phieuNhap(): BelongsTo
    {
        return $this->belongsTo(PhieuNhap::class, 'maPN');
    }

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'maSP');
    }
}
