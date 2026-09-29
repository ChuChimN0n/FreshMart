<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NhaCungCap extends Model
{
    protected $table = 'NhaCungCap';

    protected $primaryKey = 'maNCC';

    public $timestamps = false;

    protected $fillable = [
        'tenNCC',
        'codeNCC',
        'soDienThoai',
        'email',
        'diaChi',
    ];

    public function sanPhams(): HasMany
    {
        return $this->hasMany(SanPham::class, 'maNCC');
    }

    public function phieuNhaps(): HasMany
    {
        return $this->hasMany(PhieuNhap::class, 'maNCC');
    }

    public function cungCapSanPhams(): HasMany
    {
        return $this->hasMany(NhaCungCapSanPham::class, 'maNCC');
    }
}
