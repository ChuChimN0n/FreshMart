<?php

namespace Database\Factories;

use App\Models\NhaCungCap;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<NhaCungCap> */
class NhaCungCapFactory extends Factory
{
    protected $model = NhaCungCap::class;

    public function definition(): array
    {
        return ['tenNCC' => fake()->company(), 'soDienThoai' => '0901234567'];
    }
}
