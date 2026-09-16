<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaiTro extends Model
{
    const ADMIN_ID = 1;

    const STAFF_ID = 2;

    const KHACH_HANG_ID = 3;

    protected $table = 'VaiTro';

    protected $primaryKey = 'maVT';

    public $timestamps = false;

    protected $fillable = [
        'tenVT',
        'moTa',
    ];

    public function quyens(): BelongsToMany
    {
        return $this->belongsToMany(Quyen::class, 'VaiTroQuyen', 'maVT', 'maQuyen');
    }

    public function taiKhoans(): HasMany
    {
        return $this->hasMany(TaiKhoan::class, 'maVT');
    }
}
