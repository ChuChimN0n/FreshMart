<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NhaCungCapSanPham extends Model
{
    const HOAT_DONG = 'HOAT_DONG';

    const NGUNG_HOAT_DONG = 'NGUNG_HOAT_DONG';

    protected $table = 'NhaCungCapSanPham';

    public $incrementing = false;

    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'maNCC',
        'maSP',
        'trangThai',
        'ghiChu',
    ];

    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'maNCC');
    }

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'maSP');
    }
}
