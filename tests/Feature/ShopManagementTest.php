<?php

namespace Tests\Feature;

use App\Models\SanPham;
use Database\Factories\DanhMucFactory;
use Database\Factories\NhaCungCapFactory;
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

    public function test_product_price_must_be_greater_than_zero(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create());
        $ncc = NhaCungCapFactory::new()->create();
        $danhMuc = DanhMucFactory::new()->create();
        $payload = [
            'maNCC' => $ncc->maNCC,
            'maDM' => $danhMuc->maDM,
            'tenSP' => 'SP gia khong',
            'donVi' => 'kg',
            'giaBan' => 999,
            'mucTonToiThieu' => 10,
            'trangThai' => SanPham::DANG_BAN,
        ];

        $this->post(route('staff.sanpham.store'), $payload)
            ->assertSessionHasErrors(['giaBan' => 'Giá bán tối thiểu 1.000 đồng.']);
        $this->assertDatabaseMissing('SanPham', ['tenSP' => 'SP gia khong']);
    }
}
