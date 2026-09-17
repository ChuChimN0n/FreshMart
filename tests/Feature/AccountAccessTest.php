<?php

namespace Tests\Feature;

use App\Models\Quyen;
use App\Models\VaiTro;
use Database\Factories\TaiKhoanFactory;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccountAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $seed = true;

    protected $seeder = RolePermissionSeeder::class;

    public function test_locked_customer_is_logged_out_before_checkout(): void
    {
        $user = TaiKhoanFactory::new()->locked()->create();
        $this->actingAs($user)->withSession(['private-value' => 'secret'])
            ->post(route('donhang.place'), [])->assertRedirectToRoute('login')
            ->assertSessionHasErrors(['tenDangNhap' => 'Tài khoản đã bị khóa'])
            ->assertSessionMissing('private-value');
        $this->assertGuest();
        $this->assertDatabaseCount('DonHang', 0);
    }

    public function test_staff_cannot_access_admin_accounts(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->staff()->create())
            ->get(route('admin.taikhoan.index'))->assertForbidden();
    }

    public function test_removing_a_staff_permission_blocks_its_route(): void
    {
        $staff = TaiKhoanFactory::new()->staff()->create();
        VaiTro::findOrFail(VaiTro::STAFF_ID)->quyens()->detach(
            Quyen::where('tenQuyen', Quyen::PRODUCTS)->value('maQuyen')
        );
        $this->actingAs($staff)->get(route('staff.sanpham.index'))->assertForbidden();
    }

    #[DataProvider('staffPages')]
    public function test_admin_inherits_staff_functions(string $route): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->get(route($route))->assertOk();
    }

    public static function staffPages(): array
    {
        return [
            'products' => ['staff.sanpham.index'],
            'categories' => ['staff.danhmuc.index'],
            'orders' => ['staff.donhang.index'],
            'reviews' => ['staff.danhgia.index'],
        ];
    }

    public function test_custom_role_can_only_access_granted_management_functions(): void
    {
        $role = VaiTro::create(['tenVT' => 'Kho']);
        $role->quyens()->attach(Quyen::where('tenQuyen', Quyen::PRODUCTS)->value('maQuyen'));
        $user = TaiKhoanFactory::new()->create(['maVT' => $role->maVT]);

        $this->actingAs($user)->get(route('staff.sanpham.index'))->assertOk();
        $this->get(route('staff.donhang.index'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_cannot_lock_or_demote_themselves(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create(['email' => 'admin.real@gmail.com']);
        $this->actingAs($admin)->patch(route('admin.taikhoan.toggle', $admin))->assertSessionHas('error');
        $this->put(route('admin.taikhoan.update', $admin), [
            'hoTen' => $admin->hoTen, 'email' => 'admin.real@gmail.com', 'soDienThoai' => '0901234567',
            'maVT' => VaiTro::STAFF_ID, 'trangThai' => 'HOAT_DONG',
        ])->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->isActive());
        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_account_editor_cannot_promote_themselves_to_admin(): void
    {
        $role = VaiTro::create(['tenVT' => 'Nhân sự']);
        $role->quyens()->attach(Quyen::where('tenQuyen', Quyen::ACCOUNTS)->value('maQuyen'));
        $user = TaiKhoanFactory::new()->create(['maVT' => $role->maVT, 'email' => 'nhansu.real@gmail.com']);

        $this->actingAs($user)->put(route('admin.taikhoan.update', $user), [
            'hoTen' => $user->hoTen, 'email' => 'nhansu.real@gmail.com', 'soDienThoai' => '0901234567',
            'maVT' => VaiTro::ADMIN_ID, 'trangThai' => 'HOAT_DONG',
        ])->assertForbidden();
        $this->assertSame($role->maVT, $user->fresh()->maVT);
    }

    public function test_short_password_is_rejected_when_editing_an_account(): void
    {
        $staff = TaiKhoanFactory::new()->staff()->create();
        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->put(route('admin.taikhoan.update', $staff), [
                'hoTen' => $staff->hoTen, 'email' => $staff->email, 'soDienThoai' => '0901234567',
                'maVT' => VaiTro::STAFF_ID, 'trangThai' => 'HOAT_DONG', 'matKhau' => 'x',
            ])->assertSessionHasErrors('matKhau');
        $this->assertTrue(Hash::check('password123', $staff->fresh()->matKhau));
    }

    public function test_duplicate_phone_is_rejected_when_creating_an_account(): void
    {
        TaiKhoanFactory::new()->staff()->create(['soDienThoai' => '0901234567']);
        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->post(route('admin.taikhoan.store'), [
                'hoTen' => 'Nguyen Van B',
                'tenDangNhap' => 'nguyenvanb',
                'email' => 'nguyenvanb.real@gmail.com',
                'soDienThoai' => '0901234567',
                'diaChi' => 'Ha Noi',
                'maVT' => VaiTro::STAFF_ID,
                'matKhau' => 'password123',
                'matKhau_confirmation' => 'password123',
                'trangThai' => 'HOAT_DONG',
            ])->assertSessionHasErrors([
                'soDienThoai' => 'Số điện thoại này đã được sử dụng.',
            ]);
        $this->assertDatabaseMissing('TaiKhoan', ['tenDangNhap' => 'nguyenvanb']);
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        TaiKhoanFactory::new()->create(['tenDangNhap' => 'rate-limited-user']);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', ['tenDangNhap' => 'rate-limited-user', 'matKhau' => 'incorrect'])
                ->assertSessionHasErrors('tenDangNhap');
        }
        $this->post('/login', ['tenDangNhap' => 'rate-limited-user', 'matKhau' => 'incorrect'])
            ->assertTooManyRequests();
        $this->assertGuest();
    }

    public function test_login_uses_the_custom_password_column_and_redirects_to_accessible_page(): void
    {
        $user = TaiKhoanFactory::new()->staff()->create();
        $this->post('/login', ['tenDangNhap' => $user->tenDangNhap, 'matKhau' => 'password123', 'remember' => '1'])
            ->assertRedirectToRoute('staff.dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertNotEmpty($user->fresh()->remember_token);
        $this->assertSame($user->matKhau, $user->getAuthPassword());
    }

    public function test_invalid_permission_id_does_not_partially_create_a_role(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->post(route('admin.vaitro.store'), ['tenVT' => 'Invalid role', 'quyens' => [999999]])
            ->assertSessionHasErrors('quyens.0');
        $this->assertDatabaseMissing('VaiTro', ['tenVT' => 'Invalid role']);
    }

    public function test_duplicate_role_name_is_rejected_with_vietnamese_message(): void
    {
        VaiTro::create(['tenVT' => 'Kho']);
        $quyenId = Quyen::query()->value('maQuyen');
        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->post(route('admin.vaitro.store'), ['tenVT' => 'Kho', 'quyens' => [$quyenId]])
            ->assertSessionHasErrors([
                'tenVT' => 'Tên vai trò này đã tồn tại, vui lòng chọn tên khác.',
            ]);
        $this->assertSame(1, VaiTro::where('tenVT', 'Kho')->count());
    }

    public function test_role_cannot_be_created_or_updated_without_permissions(): void
    {
        $admin = TaiKhoanFactory::new()->admin()->create();
        $this->actingAs($admin)
            ->post(route('admin.vaitro.store'), ['tenVT' => 'Trong'])
            ->assertSessionHasErrors('quyens');

        $role = VaiTro::create(['tenVT' => 'Co quyen']);
        $role->quyens()->attach(Quyen::query()->value('maQuyen'));
        $this->put(route('admin.vaitro.update', $role), ['tenVT' => 'Co quyen'])
            ->assertSessionHasErrors('quyens');
        $this->assertSame(1, $role->fresh()->quyens()->count());
    }

    public function test_admin_role_can_be_updated_without_permissions(): void
    {
        $adminRole = VaiTro::findOrFail(VaiTro::ADMIN_ID);
        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->put(route('admin.vaitro.update', $adminRole), [
                'tenVT' => $adminRole->tenVT,
                'moTa' => 'Cap nhat mo ta',
            ])->assertRedirect(route('admin.vaitro.index'));
        $this->assertSame('Cap nhat mo ta', $adminRole->fresh()->moTa);
    }

    public function test_custom_role_can_be_deleted_when_unused(): void
    {
        $role = VaiTro::create(['tenVT' => 'Tam']);
        $role->quyens()->attach(Quyen::query()->value('maQuyen'));
        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->delete(route('admin.vaitro.destroy', $role))
            ->assertRedirect(route('admin.vaitro.index'));
        $this->assertDatabaseMissing('VaiTro', ['tenVT' => 'Tam']);
        $this->assertDatabaseMissing('VaiTroQuyen', ['maVT' => $role->maVT]);
    }

    public function test_system_roles_cannot_be_deleted(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->admin()->create());
        foreach ([VaiTro::ADMIN_ID, VaiTro::STAFF_ID, VaiTro::KHACH_HANG_ID] as $maVT) {
            $this->delete(route('admin.vaitro.destroy', $maVT))
                ->assertSessionHas('error');
            $this->assertNotNull(VaiTro::find($maVT));
        }
    }

    public function test_role_in_use_cannot_be_deleted(): void
    {
        $role = VaiTro::create(['tenVT' => 'Dang dung']);
        TaiKhoanFactory::new()->create(['maVT' => $role->maVT]);
        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->delete(route('admin.vaitro.destroy', $role))
            ->assertSessionHas('error');
        $this->assertNotNull($role->fresh());
    }

    public function test_role_cannot_mix_customer_and_management_permissions(): void
    {
        $quyenIds = Quyen::whereIn('tenQuyen', ['Quản lý giỏ hàng', Quyen::PRODUCTS])->pluck('maQuyen')->all();
        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->post(route('admin.vaitro.store'), ['tenVT' => 'Tron nhom', 'quyens' => $quyenIds])
            ->assertSessionHasErrors([
                'quyens' => 'Một vai trò chỉ thuộc một nhóm: Khách hàng hoặc Quản trị/Nhân viên, không được trộn lẫn.',
            ]);
        $this->assertDatabaseMissing('VaiTro', ['tenVT' => 'Tron nhom']);
    }

    public function test_only_customer_group_can_access_shopping_routes(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->create())
            ->get(route('checkout'))
            ->assertRedirectToRoute('giohang.index');

        $this->actingAs(TaiKhoanFactory::new()->staff()->create())
            ->get(route('checkout'))
            ->assertForbidden();

        $this->actingAs(TaiKhoanFactory::new()->admin()->create())
            ->get(route('checkout'))
            ->assertForbidden();
    }

    public function test_personal_profile_routes_are_open_to_every_logged_in_account(): void
    {
        $this->actingAs(TaiKhoanFactory::new()->staff()->create())
            ->get(route('profile'))
            ->assertOk();

        $this->actingAs(TaiKhoanFactory::new()->create())
            ->get(route('change-password'))
            ->assertOk();
    }

    public function test_customer_can_register_with_valid_data(): void
    {
        $this->post('/register', [
            'hoTen' => 'Khach Hang',
            'tenDangNhap' => 'khachhang1234567',
            'email' => 'k123nam13@gmail.com',
            'soDienThoai' => '0977777775',
            'diaChi' => 'Ha Noi',
            'matKhau' => 'password123',
            'matKhau_confirmation' => 'password123',
        ])->assertRedirectToRoute('home');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('TaiKhoan', ['tenDangNhap' => 'khachhang1234567']);
    }

    public function test_customer_can_change_password_with_valid_data(): void
    {
        $user = TaiKhoanFactory::new()->create();
        $this->actingAs($user)->post(route('change-password'), [
            'matKhau_hien_tai' => 'password123',
            'matKhau_moi' => 'newpass123',
            'matKhau_moi_confirmation' => 'newpass123',
        ])->assertSessionHas('success');
        $this->assertTrue(Hash::check('newpass123', $user->fresh()->matKhau));
    }
}
