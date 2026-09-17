<?php

namespace Database\Factories;

use App\Models\DanhMuc;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DanhMuc> */
class DanhMucFactory extends Factory
{
    protected $model = DanhMuc::class;

    public function definition(): array
    {
        return ['tenDM' => fake()->word()];
    }
}
