<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DanhMuc extends Model
{
    public const DISPLAY_ORDER = [
        'Rau củ', 'Rau thơm', 'Trái cây', 'Thịt heo', 'Thịt gà',
        'Hải sản', 'Trứng', 'Gạo & Ngũ cốc', 'Đồ khô & Gia vị', 'Bánh kẹo & Đồ uống',
    ];

    protected $table = 'DanhMuc';

    protected $primaryKey = 'maDM';

    public $timestamps = false;

    protected $fillable = [
        'tenDM',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        $order = implode(',', array_map(
            fn (string $name): string => "'".str_replace("'", "''", $name)."'",
            self::DISPLAY_ORDER
        ));

        return $query->orderByRaw("FIELD(tenDM, {$order}) ASC, tenDM ASC");
    }

    public function sanPhams(): HasMany
    {
        return $this->hasMany(SanPham::class, 'maDM');
    }
}
