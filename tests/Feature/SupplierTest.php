<?php

namespace Tests\Feature;

use App\Models\NhaCungCap;
use Database\Factories\SanPhamFactory;
use Database\Factories\TaiKhoanFactory;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $seed = true;

    protected $seeder = RolePermissionSeeder::class;

    public function test_duplicate_phone_shows_vietnamese_error_when_creating(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create());

        $this->post(route('admin.nhacungcap.store'), [
            'tenNCC' => 'NCC A',
            'soDienThoai' => '0901234567',
            'email' => 'ncca.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ])->assertRedirect(route('admin.nhacungcap.index'));

        $this->post(route('admin.nhacungcap.store'), [
            'tenNCC' => 'NCC B',
            'soDienThoai' => '0901234567',
            'email' => 'nccb.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ])->assertSessionHasErrors([
            'soDienThoai' => 'Số điện thoại đã được sử dụng',
        ]);

        $this->assertSame(1, NhaCungCap::where('soDienThoai', '0901234567')->count());
    }

    public function test_updating_without_changing_phone_and_email_succeeds(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create());

        $ncc = NhaCungCap::create([
            'tenNCC' => 'NCC A',
            'soDienThoai' => '0901234567',
            'email' => 'ncca.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ]);

        $this->put(route('admin.nhacungcap.update', $ncc), [
            'tenNCC' => 'NCC A Updated',
            'soDienThoai' => '0901234567',
            'email' => 'ncca.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ])->assertRedirect(route('admin.nhacungcap.index'));

        $this->assertSame('NCC A Updated', $ncc->fresh()->tenNCC);
    }

    public function test_updating_to_another_suppliers_phone_is_rejected(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create());

        NhaCungCap::create([
            'tenNCC' => 'NCC A',
            'soDienThoai' => '0901234567',
            'email' => 'ncca.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ]);
        $nccB = NhaCungCap::create([
            'tenNCC' => 'NCC B',
            'soDienThoai' => '0912345678',
            'email' => 'nccb.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ]);

        $this->put(route('admin.nhacungcap.update', $nccB), [
            'tenNCC' => 'NCC B',
            'soDienThoai' => '0901234567',
            'email' => 'nccb.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ])->assertSessionHasErrors([
            'soDienThoai' => 'Số điện thoại đã được sử dụng',
        ]);
    }

    public function test_deleting_supplier_with_products_is_rejected_with_vietnamese_message(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create());

        $ncc = NhaCungCap::create([
            'tenNCC' => 'NCC A',
            'soDienThoai' => '0901234567',
            'email' => 'ncca.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ]);
        SanPhamFactory::new()->create(['maNCC' => $ncc->maNCC]);

        $this->from(route('admin.nhacungcap.index'))
            ->delete(route('admin.nhacungcap.destroy', $ncc))
            ->assertRedirect(route('admin.nhacungcap.index'))
            ->assertSessionHas('error', 'Nhà cung cấp này đã được sử dụng trong sản phẩm, không thể xóa!');

        $this->assertNotNull($ncc->fresh());
    }

    public function test_deleting_supplier_without_products_succeeds(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create());

        $ncc = NhaCungCap::create([
            'tenNCC' => 'NCC A',
            'soDienThoai' => '0901234567',
            'email' => 'ncca.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ]);

        $this->from(route('admin.nhacungcap.index'))
            ->delete(route('admin.nhacungcap.destroy', $ncc))
            ->assertRedirect(route('admin.nhacungcap.index'))
            ->assertSessionHas('success', 'Xóa nhà cung cấp thành công!');

        $this->assertNull($ncc->fresh());
    }

    public function test_supplier_forms_render_with_preview_panel(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create());

        $ncc = NhaCungCap::create([
            'tenNCC' => 'NCC Preview',
            'soDienThoai' => '0901234567',
            'email' => 'preview.real@gmail.com',
            'diaChi' => 'Ha Noi',
        ]);

        $this->get(route('admin.nhacungcap.create'))
            ->assertOk()
            ->assertSee('Xem trước nhà cung cấp', false);
        $this->get(route('admin.nhacungcap.edit', $ncc))
            ->assertOk()
            ->assertSee('Xem trước nhà cung cấp', false);
    }
}
