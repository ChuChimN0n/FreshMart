<?php

namespace Tests\Feature;

use App\Models\DanhGia;
use App\Models\DonHang;
use App\Models\TaiKhoan;
use Database\Factories\ChiTietDonHangFactory;
use Database\Factories\ChiTietGioHangFactory;
use Database\Factories\DonHangFactory;
use Database\Factories\SanPhamFactory;
use Database\Factories\TaiKhoanFactory;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OrderIntegrityTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $seed = true;

    protected $seeder = RolePermissionSeeder::class;

    public function test_checkout_uses_current_prices_and_cannot_consume_the_same_cart_twice(): void
    {
        $user = TaiKhoanFactory::new()->create();
        $product = SanPhamFactory::new()->create(['giaBan' => 30000]);
        $cart = $user->gioHang()->create(['tongTien' => 50000]);
        ChiTietGioHangFactory::new()->create(['maGioHang' => $cart->maGioHang, 'maSP' => $product->maSP]);

        $this->actingAs($user)->post(route('donhang.place'), $this->address())
            ->assertRedirectToRoute('donhang.index')->assertSessionHas('success');
        $this->assertDatabaseHas('DonHang', ['maTK' => $user->maTK, 'tongTien' => 60000]);
        $this->assertDatabaseHas('ChiTietDonHang', ['maSP' => $product->maSP, 'donGia' => 30000]);
        $this->assertSame(8, $product->fresh()->soLuong);
        $this->assertDatabaseCount('ChiTietGioHang', 0);

        $this->post(route('donhang.place'), $this->address())->assertSessionHas('error');
        $this->assertDatabaseCount('DonHang', 1);
        $this->assertSame(8, $product->fresh()->soLuong);
    }

    public function test_insufficient_stock_rolls_back_all_previous_stock_changes(): void
    {
        $user = TaiKhoanFactory::new()->create();
        $cart = $user->gioHang()->create(['tongTien' => 100000]);
        $available = SanPhamFactory::new()->create();
        $unavailable = SanPhamFactory::new()->create(['soLuong' => 1]);
        foreach ([$available, $unavailable] as $product) {
            ChiTietGioHangFactory::new()->create(['maGioHang' => $cart->maGioHang, 'maSP' => $product->maSP]);
        }

        $this->actingAs($user)->post(route('donhang.place'), $this->address())->assertSessionHas('error');

        $this->assertSame(10, $available->fresh()->soLuong);
        $this->assertSame(1, $unavailable->fresh()->soLuong);
        $this->assertDatabaseCount('DonHang', 0);
        $this->assertDatabaseCount('ChiTietDonHang', 0);
        $this->assertDatabaseCount('ChiTietGioHang', 2);
    }

    public function test_customer_cancellation_restocks_once_and_repeated_requests_are_rejected(): void
    {
        $user = TaiKhoanFactory::new()->create();
        $order = $this->orderFor($user);
        $product = $order->chiTietDonHangs()->first()->sanPham;

        $this->actingAs($user)->patch(route('donhang.cancel', $order))->assertSessionHas('success');
        $this->patch(route('donhang.cancel', $order))->assertSessionHas('error');

        $this->assertSame(12, $product->fresh()->soLuong);
        $this->assertSame(DonHang::DA_HUY, $order->fresh()->trangThai);
    }

    public function test_stale_order_cannot_restock_twice_or_overwrite_a_newer_status(): void
    {
        $order = $this->orderFor(TaiKhoanFactory::new()->create());
        $stale = DonHang::findOrFail($order->maDH);
        $product = $order->chiTietDonHangs()->first()->sanPham;

        $this->assertTrue($order->transitionTo(DonHang::DA_HUY));
        $this->assertFalse($stale->transitionTo(DonHang::DA_HUY));
        $this->assertFalse($stale->transitionTo(DonHang::DA_XAC_NHAN));
        $this->assertSame(12, $product->fresh()->soLuong);
    }

    public function test_customer_cannot_cancel_after_staff_confirmation_even_with_a_stale_model(): void
    {
        $order = $this->orderFor(TaiKhoanFactory::new()->create());
        $stale = DonHang::findOrFail($order->maDH);
        $this->assertTrue($order->transitionTo(DonHang::DA_XAC_NHAN));
        $this->assertFalse($stale->transitionTo(DonHang::DA_HUY, customerCancellation: true));
        $this->assertSame(DonHang::DA_XAC_NHAN, $order->fresh()->trangThai);
    }

    public function test_customer_cannot_read_or_cancel_someone_elses_order(): void
    {
        $order = $this->orderFor(TaiKhoanFactory::new()->create());
        $this->actingAs(TaiKhoanFactory::new()->create())
            ->get(route('donhang.detail', $order))->assertForbidden();
        $this->patch(route('donhang.cancel', $order))->assertForbidden();
        $this->assertSame(DonHang::CHO_XAC_NHAN, $order->fresh()->trangThai);
    }

    public function test_cart_displays_current_prices_and_rejects_updates_from_another_customer(): void
    {
        $owner = TaiKhoanFactory::new()->create();
        $cart = $owner->gioHang()->create(['tongTien' => 50000]);
        $product = SanPhamFactory::new()->create(['giaBan' => 30000]);
        $detail = ChiTietGioHangFactory::new()->create(['maGioHang' => $cart->maGioHang, 'maSP' => $product->maSP]);

        $this->actingAs($owner)->get(route('giohang.index'))->assertOk()
            ->assertViewHas('pricesChanged', true)
            ->assertViewHas('gioHang', fn ($value): bool => (float) $value->tongTien === 60000.0);

        $this->actingAs(TaiKhoanFactory::new()->create())
            ->put(route('giohang.update', $detail), ['soLuong' => 5])->assertForbidden();
        $this->delete(route('giohang.remove', $detail))->assertForbidden();
        $this->assertSame(2, $detail->fresh()->soLuong);
    }

    public function test_cancel_dang_giao_order_shows_specific_message(): void
    {
        $user = TaiKhoanFactory::new()->create();
        $order = $this->orderFor($user);
        $this->assertTrue($order->transitionTo(DonHang::DA_XAC_NHAN));
        $this->assertTrue($order->transitionTo(DonHang::DANG_GIAO));

        $this->actingAs($user)->patch(route('donhang.cancel', $order))
            ->assertSessionHas('error', 'Đơn hàng đang giao, không thể hủy!');
        $this->assertSame(DonHang::DANG_GIAO, $order->fresh()->trangThai);
    }

    private function orderFor(TaiKhoan $user): DonHang
    {
        $order = DonHangFactory::new()->create(['maTK' => $user->maTK]);
        ChiTietDonHangFactory::new()->create(['maDH' => $order->maDH]);

        return $order;
    }

    public function test_completed_order_detail_renders_when_first_item_is_not_reviewed(): void
    {
        $user = TaiKhoanFactory::new()->create();
        $order = DonHangFactory::new()->create(['maTK' => $user->maTK, 'trangThai' => DonHang::HOAN_THANH]);
        $first = ChiTietDonHangFactory::new()->create(['maDH' => $order->maDH]);
        $second = ChiTietDonHangFactory::new()->create(['maDH' => $order->maDH]);
        DanhGia::create([
            'maTK' => $user->maTK,
            'maSP' => $second->maSP,
            'soSao' => 5,
            'noiDung' => 'Rat ngon',
        ]);

        $this->actingAs($user)->get(route('donhang.detail', $order))
            ->assertOk()
            ->assertSee('Đánh giá sản phẩm')
            ->assertSee('Đã đánh giá');

        $this->assertSame($first->maSP, $order->chiTietDonHangs()->orderBy('maSP')->first()->maSP);
    }

    /** @return array{tenNguoiNhan: string, soDienThoai: string, diaChi: string} */
    private function address(): array
    {
        return ['tenNguoiNhan' => 'Nguyễn An', 'soDienThoai' => '0901234567', 'diaChi' => 'Hà Nội'];
    }
}
