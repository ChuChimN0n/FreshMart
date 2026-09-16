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
        $tong = $this->chiTietGioHangs->sum('thanhTien');
        $this->update(['tongTien' => $tong]);

        return $tong;
    }
}
