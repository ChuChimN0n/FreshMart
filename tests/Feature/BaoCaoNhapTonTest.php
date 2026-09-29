<?php

namespace Tests\Feature;

use App\Models\NhaCungCapSanPham;
use App\Models\PhieuNhap;
use Database\Factories\PhieuNhapFactory;
use Database\Factories\SanPhamFactory;
use Database\Factories\TaiKhoanFactory;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BaoCaoNhapTonTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $seed = true;

    protected $seeder = RolePermissionSeeder::class;

    private function confirmedPhieu(): PhieuNhap
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $product = SanPhamFactory::new()->create(['soLuong' => 10]);
        NhaCungCapSanPham::create(['maNCC' => $product->maNCC, 'maSP' => $product->maSP]);
        $phieu = PhieuNhapFactory::new()->create(['maNCC' => $product->maNCC, 'maNguoiTao' => $admin->maTK]);
        $phieu->chiTiets()->create(['maSP' => $product->maSP, 'soLuong' => 4, 'giaNhap' => 20000, 'thanhTien' => 80000]);
        $this->assertTrue($phieu->xacNhan());

        return $phieu->fresh();
    }

    public function test_nhaphang_report_shows_only_confirmed_phieu(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $confirmed = $this->confirmedPhieu();
        $draft = PhieuNhapFactory::new()->create();

        $range = ['tuNgay' => now()->subDay()->format('Y-m-d'), 'denNgay' => now()->addDay()->format('Y-m-d')];
        $response = $this->actingAs($admin)->get(route('admin.baocao.nhaphang', $range))->assertOk();

        $response->assertSee($confirmed->maPhieu)->assertDontSee($draft->maPhieu);
        $response->assertSee('80.000đ');
    }

    public function test_nhaphang_report_rejects_invalid_range(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();

        $this->actingAs($admin)->get(route('admin.baocao.nhaphang', [
            'tuNgay' => now()->format('Y-m-d'),
            'denNgay' => now()->subDay()->format('Y-m-d'),
        ]))->assertSessionHasErrors('denNgay');
    }

    public function test_tonkho_report_groups_by_stock_status(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        SanPhamFactory::new()->create(['tenSP' => 'Ton bao cao het', 'soLuong' => 0, 'mucTonToiThieu' => 10]);
        SanPhamFactory::new()->create(['tenSP' => 'Ton bao cao sap het', 'soLuong' => 5, 'mucTonToiThieu' => 10]);

        $response = $this->actingAs($admin)->get(route('admin.baocao.tonkho'))->assertOk();

        $response->assertSee('Hết hàng')->assertSee('Sắp hết hàng')->assertSee('Còn hàng');
    }

    public function test_reports_require_permissions(): void
    {
        $staff = TaiKhoanFactory::new()->staff()->create();
        $customer = TaiKhoanFactory::new()->create();

        foreach (['admin.baocao.nhaphang', 'admin.baocao.tonkho'] as $route) {
            $params = $route === 'admin.baocao.nhaphang'
                ? ['tuNgay' => now()->subDay()->format('Y-m-d'), 'denNgay' => now()->format('Y-m-d')]
                : [];
            $this->actingAs($staff)->get(route($route, $params))->assertForbidden();
            $this->actingAs($customer)->get(route($route, $params))->assertForbidden();
        }
    }
}
