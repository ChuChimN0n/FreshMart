<?php

namespace Tests\Feature;

use Database\Factories\DanhMucFactory;
use Database\Factories\TaiKhoanFactory;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ShopManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $seed = true;

    protected $seeder = RolePermissionSeeder::class;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_category_forms_render_with_preview_panel(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->staff()->create());

        $danhMuc = DanhMucFactory::new()->create();

        $this->get(route('staff.danhmuc.create'))
            ->assertOk()
            ->assertSee('Xem trước hiển thị', false);
        $this->get(route('staff.danhmuc.edit', $danhMuc))
            ->assertOk()
            ->assertSee('Xem trước hiển thị', false);
    }
}
