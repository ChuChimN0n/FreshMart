<?php

namespace Tests\Feature;

use App\Models\LichSuKho;
use App\Models\NhaCungCapSanPham;
use App\Models\PhieuNhap;
use App\Models\SanPham;
use Database\Factories\ChiTietDonHangFactory;
use Database\Factories\DanhMucFactory;
use Database\Factories\DonHangFactory;
use Database\Factories\NhaCungCapFactory;
use Database\Factories\PhieuNhapFactory;
use Database\Factories\SanPhamFactory;
use Database\Factories\TaiKhoanFactory;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PhieuNhapTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $seed = true;

    protected $seeder = RolePermissionSeeder::class;

    public function test_store_creates_phieu_without_increasing_stock(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $product = SanPhamFactory::new()->create(['soLuong' => 10]);
        NhaCungCapSanPham::create([
            'maNCC' => $product->maNCC,
            'maSP' => $product->maSP,
            'trangThai' => NhaCungCapSanPham::HOAT_DONG,
        ]);

        $this->actingAs($admin)->post(route('staff.nhaphang.store'), [
            'maNCC' => $product->maNCC,
            'ghiChu' => 'Nhập test',
            'items' => [
                ['maSP' => $product->maSP, 'soLuong' => 5, 'giaNhap' => 20000],
            ],
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('PhieuNhap', ['maNCC' => $product->maNCC, 'trangThai' => PhieuNhap::NHAP]);
        // Chưa xác nhận: tồn kho giữ nguyên.
        $this->assertSame(10, $product->fresh()->soLuong);
        $this->assertDatabaseCount('LichSuKho', 0);
    }

    public function test_confirm_increases_stock_once_and_writes_history(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $product = SanPhamFactory::new()->create(['soLuong' => 10]);
        $phieu = PhieuNhapFactory::new()->create(['maNCC' => $product->maNCC, 'maNguoiTao' => $admin->maTK]);
        $phieu->chiTiets()->create(['maSP' => $product->maSP, 'soLuong' => 5, 'giaNhap' => 20000, 'thanhTien' => 100000]);
        NhaCungCapSanPham::create(['maNCC' => $product->maNCC, 'maSP' => $product->maSP]);

        $this->actingAs($admin)->patch(route('staff.nhaphang.confirm', $phieu))
            ->assertRedirect()->assertSessionHas('success');

        $this->assertSame(15, $product->fresh()->soLuong);
        $this->assertSame(PhieuNhap::DA_XAC_NHAN, $phieu->fresh()->trangThai);
        $this->assertDatabaseHas('LichSuKho', [
            'maSP' => $product->maSP,
            'loaiBienDong' => LichSuKho::NHAP_HANG,
            'soLuong' => 5,
            'tonTruoc' => 10,
            'tonSau' => 15,
            'maPN' => $phieu->maPN,
        ]);

        // Xác nhận lần 2 bị từ chối, tồn không tăng thêm.
        $this->patch(route('staff.nhaphang.confirm', $phieu))->assertSessionHas('error');
        $this->assertSame(15, $product->fresh()->soLuong);
    }

    public function test_store_rejects_product_not_supplied_by_supplier(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $product = SanPhamFactory::new()->create();
        $otherNcc = NhaCungCapFactory::new()->create();

        $this->actingAs($admin)->post(route('staff.nhaphang.store'), [
            'maNCC' => $otherNcc->maNCC,
            'items' => [
                ['maSP' => $product->maSP, 'soLuong' => 2, 'giaNhap' => 10000],
            ],
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('PhieuNhap', 0);
        $this->assertSame(10, $product->fresh()->soLuong);
    }

    public function test_store_validates_items(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $product = SanPhamFactory::new()->create();

        $this->actingAs($admin)->post(route('staff.nhaphang.store'), [
            'maNCC' => $product->maNCC,
            'items' => [
                ['maSP' => $product->maSP, 'soLuong' => 0, 'giaNhap' => 10000],
            ],
        ])->assertSessionHasErrors('items.0.soLuong');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->post(route('staff.nhaphang.store'), [])->assertRedirectToRoute('login');
        $this->assertGuest();
    }

    public function test_staff_without_permission_is_forbidden(): void
    {
        $customer = TaiKhoanFactory::new()->create();

        $this->actingAs($customer)->get(route('staff.nhaphang.index'))->assertForbidden();
    }

    public function test_cancelled_order_restocks_and_sets_ngay_huy(): void
    {
        $user = TaiKhoanFactory::new()->create();
        $product = SanPhamFactory::new()->create(['soLuong' => 10]);
        $order = DonHangFactory::new()->create(['maTK' => $user->maTK]);
        ChiTietDonHangFactory::new()->create(['maDH' => $order->maDH, 'maSP' => $product->maSP, 'soLuong' => 2]);

        $this->actingAs($user)->patch(route('donhang.cancel', $order))->assertSessionHas('success');

        $this->assertSame(12, $product->fresh()->soLuong);
        $this->assertNotNull($order->fresh()->ngayHuy);
        $this->assertDatabaseHas('LichSuKho', [
            'maSP' => $product->maSP,
            'loaiBienDong' => LichSuKho::HOAN_DON,
            'maDH' => $order->maDH,
        ]);
    }

    public function test_index_and_create_pages_render(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();

        $this->actingAs($admin)->get(route('staff.nhaphang.index'))
            ->assertOk()->assertSee('Nhập hàng');
        $this->actingAs($admin)->get(route('staff.nhaphang.create'))
            ->assertOk()->assertSee('Tạo phiếu nhập', false);
    }

    public function test_low_stock_helper_uses_muc_ton_toi_thieu(): void
    {
        $sapHet = SanPhamFactory::new()->create(['soLuong' => 10, 'mucTonToiThieu' => 10]);
        $conHang = SanPhamFactory::new()->create(['soLuong' => 50, 'mucTonToiThieu' => 10]);

        $this->assertTrue($sapHet->isLowStock());
        $this->assertFalse($conHang->isLowStock());
        $this->assertDatabaseHas('SanPham', ['maSP' => $sapHet->maSP, 'mucTonToiThieu' => 10]);
    }

    public function test_creating_product_auto_maps_supplier_for_import(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $ncc = NhaCungCapFactory::new()->create();
        $danhMuc = DanhMucFactory::new()->create();

        $this->actingAs($admin)->post(route('staff.sanpham.store'), [
            'maNCC' => $ncc->maNCC,
            'maDM' => $danhMuc->maDM,
            'tenSP' => 'Rau test mapping',
            'donVi' => 'kg',
            'giaBan' => 15000,
            'soLuong' => 99,
            'mucTonToiThieu' => 7,
            'trangThai' => SanPham::DANG_BAN,
        ])->assertRedirectToRoute('staff.sanpham.index')->assertSessionHas('success');

        $maSP = SanPham::where('tenSP', 'Rau test mapping')->value('maSP');
        $this->assertNotNull($maSP);
        // soLuong client gửi lên bị bỏ qua, tồn luôn 0.
        $this->assertSame(0, SanPham::whereKey($maSP)->value('soLuong'));
        $this->assertDatabaseHas('NhaCungCapSanPham', [
            'maNCC' => $ncc->maNCC,
            'maSP' => $maSP,
            'trangThai' => NhaCungCapSanPham::HOAT_DONG,
        ]);
    }

    public function test_changing_product_supplier_adds_new_mapping_and_keeps_old(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $product = SanPhamFactory::new()->create();
        $oldNCC = $product->maNCC;
        $newNcc = NhaCungCapFactory::new()->create();
        NhaCungCapSanPham::create(['maNCC' => $oldNCC, 'maSP' => $product->maSP]);

        $this->actingAs($admin)->put(route('staff.sanpham.update', $product), [
            'maNCC' => $newNcc->maNCC,
            'maDM' => $product->maDM,
            'tenSP' => $product->tenSP,
            'donVi' => $product->donVi,
            'giaBan' => $product->giaBan,
            'soLuong' => 999,
            'mucTonToiThieu' => 12,
            'trangThai' => $product->trangThai,
        ])->assertRedirectToRoute('staff.sanpham.index')->assertSessionHas('success');

        // soLuong gửi lên bị bỏ qua, ngưỡng được cập nhật.
        $this->assertSame(10, $product->fresh()->soLuong);
        $this->assertSame(12, $product->fresh()->mucTonToiThieu);

        // Cặp mới có, cặp cũ giữ lại cho lịch sử phiếu nhập.
        $this->assertDatabaseHas('NhaCungCapSanPham', ['maNCC' => $newNcc->maNCC, 'maSP' => $product->maSP]);
        $this->assertDatabaseHas('NhaCungCapSanPham', ['maNCC' => $oldNCC, 'maSP' => $product->maSP]);
    }

    public function test_cannot_delete_supplier_with_phieu_nhap(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $ncc = NhaCungCapFactory::new()->create();
        PhieuNhapFactory::new()->create(['maNCC' => $ncc->maNCC]);

        $this->actingAs($admin)->delete(route('admin.nhacungcap.destroy', $ncc))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('NhaCungCap', ['maNCC' => $ncc->maNCC]);
    }

    public function test_delete_new_product_also_removes_supplier_mapping(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $product = SanPhamFactory::new()->create();
        NhaCungCapSanPham::create(['maNCC' => $product->maNCC, 'maSP' => $product->maSP]);

        $this->actingAs($admin)->delete(route('staff.sanpham.destroy', $product))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('SanPham', ['maSP' => $product->maSP]);
        $this->assertDatabaseMissing('NhaCungCapSanPham', ['maSP' => $product->maSP]);
    }

    public function test_cannot_delete_product_with_phieu_nhap(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $product = SanPhamFactory::new()->create();
        $phieu = PhieuNhapFactory::new()->create(['maNCC' => $product->maNCC]);
        $phieu->chiTiets()->create([
            'maSP' => $product->maSP, 'soLuong' => 3, 'giaNhap' => 10000, 'thanhTien' => 30000,
        ]);

        $this->actingAs($admin)->delete(route('staff.sanpham.destroy', $product))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('SanPham', ['maSP' => $product->maSP]);
    }

    public function test_kho_pages_render_and_respect_permissions(): void
    {
        $staff = TaiKhoanFactory::new()->staff()->create();
        $customer = TaiKhoanFactory::new()->create();

        foreach (['staff.kho.index', 'staff.kho.history', 'staff.kho.alerts'] as $route) {
            $this->actingAs($staff)->get(route($route))->assertOk();
            $this->actingAs($customer)->get(route($route))->assertForbidden();
        }
    }

    public function test_kho_pages_redirect_guest_to_login(): void
    {
        $this->get(route('staff.kho.index'))->assertRedirectToRoute('login');
        $this->assertGuest();
    }

    public function test_kho_history_shows_nhap_hang_with_before_after_stock(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $product = SanPhamFactory::new()->create(['soLuong' => 10]);
        $phieu = PhieuNhapFactory::new()->create(['maNCC' => $product->maNCC, 'maNguoiTao' => $admin->maTK]);
        $phieu->chiTiets()->create(['maSP' => $product->maSP, 'soLuong' => 4, 'giaNhap' => 20000, 'thanhTien' => 80000]);

        $this->assertTrue($phieu->xacNhan());

        $this->actingAs($admin)->get(route('staff.kho.history'))
            ->assertOk()
            ->assertSee('Nhập hàng')
            ->assertSee('10 → 14');

        $this->actingAs($admin)->get(route('staff.kho.history', ['loaiBienDong' => LichSuKho::BAN_HANG]))
            ->assertOk()
            ->assertDontSee('10 → 14');
    }

    public function test_kho_alerts_groups_out_of_stock_and_low_stock(): void
    {
        $staff = TaiKhoanFactory::new()->staff()->create();
        $hetHang = SanPhamFactory::new()->create(['tenSP' => 'SP het hang duy nhat', 'soLuong' => 0, 'mucTonToiThieu' => 10]);
        $sapHet = SanPhamFactory::new()->create(['tenSP' => 'SP sap het duy nhat', 'soLuong' => 8, 'mucTonToiThieu' => 10]);
        $conHang = SanPhamFactory::new()->create(['tenSP' => 'SP con hang duy nhat', 'soLuong' => 50, 'mucTonToiThieu' => 10]);

        $response = $this->actingAs($staff)->get(route('staff.kho.alerts'))->assertOk();
        $response->assertSee($hetHang->tenSP)->assertSee($sapHet->tenSP)->assertDontSee($conHang->tenSP);

        $this->actingAs($staff)->get(route('staff.kho.alerts', ['trangThaiTon' => 'het']))
            ->assertOk()->assertSee($hetHang->tenSP)->assertDontSee($sapHet->tenSP);
    }
}
