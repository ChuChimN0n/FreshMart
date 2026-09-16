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
        'soDienThoai',
        'email',
        'diaChi',
    ];

    public function sanPhams(): HasMany
    {
        return $this->hasMany(SanPham::class, 'maNCC');
    }
}
