<?php

namespace Database\Seeders;

use App\Models\Quyen;
use App\Models\VaiTro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ([
                VaiTro::ADMIN_ID => ['Quản lý', [...Quyen::ADMIN_PERMISSIONS, ...Quyen::STAFF_PERMISSIONS]],
                VaiTro::STAFF_ID => ['Nhân viên', Quyen::STAFF_PERMISSIONS],
                VaiTro::KHACH_HANG_ID => ['Khách hàng', Quyen::CUSTOMER_PERMISSIONS],
            ] as $id => [$name, $permissions]) {
                $role = VaiTro::firstOrCreate(['maVT' => $id], ['tenVT' => $name]);
                $permissionIds = [];
                foreach ($permissions as $permission) {
                    $permissionIds[] = Quyen::firstOrCreate(['tenQuyen' => $permission])->maQuyen;
                }

                if ($role->wasRecentlyCreated) {
                    $role->quyens()->sync($permissionIds);
                }
            }
        });
    }
}
