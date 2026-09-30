<?php

namespace Tests\Feature;

use App\Models\DonHang;
use App\Models\NhaCungCapSanPham;
use App\Models\PhieuNhap;
use Database\Factories\ChiTietDonHangFactory;
use Database\Factories\DonHangFactory;
use Database\Factories\PhieuNhapFactory;
use Database\Factories\SanPhamFactory;
use Database\Factories\TaiKhoanFactory;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
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

    /**
     * @return array<int, array<int, mixed>>
     */
    private function readXlsx(string $content): array
    {
        $temp = tempnam(sys_get_temp_dir(), 'test-xlsx').'.xlsx';
        file_put_contents($temp, $content);
        $rows = IOFactory::load($temp)->getActiveSheet()->toArray();
        unlink($temp);

        return $rows;
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

    public function test_reports_default_to_last_30_days_without_params(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();

        foreach (['admin.baocao.sanpham', 'admin.baocao.donhang', 'admin.baocao.doanhthu', 'admin.baocao.nhaphang'] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }
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

    public function test_export_nhaphang_returns_xlsx_with_confirmed_data(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $confirmed = $this->confirmedPhieu();
        $range = ['tuNgay' => now()->subDay()->format('Y-m-d'), 'denNgay' => now()->addDay()->format('Y-m-d')];

        $response = $this->actingAs($admin)->get(route('admin.baocao.nhaphang.export', $range));

        $response->assertOk();
        $this->assertStringContainsString(
            'spreadsheetml',
            $response->headers->get('Content-Type')
        );
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));

        $rows = $this->readXlsx($response->getContent());
        $this->assertSame('BÁO CÁO NHẬP HÀNG', $rows[0][0]);
        $this->assertContains('Mã phiếu nhập', $rows[3]);
        $flat = implode(' ', array_map(fn ($r) => implode(' ', array_map(strval(...), $r)), $rows));
        $this->assertStringContainsString($confirmed->maPhieu, $flat);
        $this->assertStringContainsString('Tổng cộng', $flat);
    }

    public function test_export_tonkho_returns_xlsx(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        SanPhamFactory::new()->create(['tenSP' => 'Xuat ton kho SP']);

        $response = $this->actingAs($admin)->get(route('admin.baocao.tonkho.export'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('Content-Type'));

        $rows = $this->readXlsx($response->getContent());
        $this->assertSame('BÁO CÁO TỒN KHO', $rows[0][0]);
        $flat = implode(' ', array_map(fn ($r) => implode(' ', array_map(strval(...), $r)), $rows));
        $this->assertStringContainsString('Xuat ton kho SP', $flat);
    }

    public function test_export_old_reports_return_xlsx(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $user = TaiKhoanFactory::new()->create();
        $order = DonHangFactory::new()->create([
            'maTK' => $user->maTK,
            'trangThai' => DonHang::HOAN_THANH,
            'ngayDat' => now(),
            'tongTien' => 60000,
        ]);
        ChiTietDonHangFactory::new()->create(['maDH' => $order->maDH, 'soLuong' => 2, 'donGia' => 30000, 'thanhTien' => 60000]);
        $range = ['tuNgay' => now()->subDay()->format('Y-m-d'), 'denNgay' => now()->addDay()->format('Y-m-d')];

        foreach (['admin.baocao.sanpham.export', 'admin.baocao.donhang.export', 'admin.baocao.doanhthu.export'] as $route) {
            $response = $this->actingAs($admin)->get(route($route, $range));
            $response->assertOk();
            $this->assertStringContainsString('spreadsheetml', $response->headers->get('Content-Type'));
            $this->assertStringStartsWith('PK', $response->getContent());
        }
    }

    public function test_repeated_exports_all_succeed_without_collision(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $this->confirmedPhieu();
        $range = ['tuNgay' => now()->subDay()->format('Y-m-d'), 'denNgay' => now()->addDay()->format('Y-m-d')];

        // Xuất liên tiếp 3 lần: mỗi lần đều 200 + file xlsx hợp lệ, không trùng/hỏng.
        for ($i = 0; $i < 3; $i++) {
            $response = $this->actingAs($admin)->get(route('admin.baocao.nhaphang.export', $range));
            $response->assertOk();
            $this->assertStringStartsWith('PK', $response->getContent());
            $rows = $this->readXlsx($response->getContent());
            $this->assertSame('BÁO CÁO NHẬP HÀNG', $rows[0][0]);
        }
    }

    public function test_export_empty_range_redirects_with_error(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $range = ['tuNgay' => '2000-01-01', 'denNgay' => '2000-01-02'];

        $this->actingAs($admin)->get(route('admin.baocao.nhaphang.export', $range))
            ->assertRedirect()
            ->assertSessionHas('error', 'Không có dữ liệu để xuất báo cáo.');
    }

    public function test_export_rejects_invalid_range_and_forbidden_roles(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $staff = TaiKhoanFactory::new()->staff()->create();
        $range = ['tuNgay' => now()->subDay()->format('Y-m-d'), 'denNgay' => now()->addDay()->format('Y-m-d')];

        $this->actingAs($admin)->get(route('admin.baocao.nhaphang.export', [
            'tuNgay' => now()->format('Y-m-d'),
            'denNgay' => now()->subDay()->format('Y-m-d'),
        ]))->assertSessionHasErrors('denNgay');

        $this->actingAs($staff)->get(route('admin.baocao.nhaphang.export', $range))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.baocao.tonkho.export'))->assertForbidden();
    }
}
