<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DanhGia extends Model
{
    protected $table = 'DanhGia';

    protected $primaryKey = 'maDanhGia';

    public $timestamps = false;

    protected $fillable = [
        'maTK',
        'maSP',
        'soSao',
        'noiDung',
    ];

    protected function casts(): array
    {
        return [
            'soSao' => 'integer',
        ];
    }

    public function taiKhoan(): BelongsTo
    {
        return $this->belongsTo(TaiKhoan::class, 'maTK');
    }

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'maSP');
    }
}
