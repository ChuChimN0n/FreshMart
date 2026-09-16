<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quyen extends Model
{
    protected $table = 'Quyen';

    protected $primaryKey = 'maQuyen';

    public $timestamps = false;

    protected $fillable = [
        'tenQuyen',
    ];

    public function vaiTros(): BelongsToMany
    {
        return $this->belongsToMany(VaiTro::class, 'VaiTroQuyen', 'maQuyen', 'maVT');
    }
}
