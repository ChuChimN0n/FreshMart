<?php

namespace Tests\Feature;

use App\Models\NhaCungCap;
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
}
