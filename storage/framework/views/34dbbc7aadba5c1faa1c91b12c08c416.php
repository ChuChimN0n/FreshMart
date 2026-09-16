<?php $__env->startSection('title', 'Quản lý tài khoản'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Quản lý tài khoản</h1>
    <a href="<?php echo e(route('admin.taikhoan.create')); ?>" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">+ Thêm tài khoản</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm tài khoản..." class="border rounded px-3 py-2 flex-1">
    <select name="maVT" class="border rounded px-3 py-2">
        <option value="">-- Tất cả vai trò --</option>
        <?php $__currentLoopData = $vaiTros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($vt->maVT); ?>" <?php echo e(request('maVT') == $vt->maVT ? 'selected' : ''); ?>><?php echo e($vt->tenVT); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Mã TK</th>
                <th class="px-4 py-2 text-left">Họ tên</th>
                <th class="px-4 py-2 text-left">Tên ĐN</th>
                <th class="px-4 py-2 text-left">Email</th>
                <th class="px-4 py-2 text-left">Vai trò</th>
                <th class="px-4 py-2 text-left">Trạng thái</th>
                <th class="px-4 py-2 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $taiKhoans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo e($tk->maTK); ?></td>
                <td class="px-4 py-2"><?php echo e($tk->hoTen); ?></td>
                <td class="px-4 py-2"><?php echo e($tk->tenDangNhap); ?></td>
                <td class="px-4 py-2"><?php echo e($tk->email); ?></td>
                <td class="px-4 py-2"><?php echo e($tk->vaiTro->tenVT ?? ''); ?></td>
                <td class="px-4 py-2">
                    <?php if($tk->trangThai == 'HOAT_DONG'): ?>
                        <span class="text-bhx-600">Hoạt động</span>
                    <?php else: ?>
                        <span class="text-red-600">Khóa</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-2 flex gap-2">
                    <a href="<?php echo e(route('admin.taikhoan.edit', $tk)); ?>" class="text-blue-600 hover:underline"><?php echo e($tk->isCustomer() ? 'Xem' : 'Sửa'); ?></a>
                    <form method="POST" action="<?php echo e(route('admin.taikhoan.toggle', $tk)); ?>" class="inline">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <?php if($tk->trangThai == 'HOAT_DONG'): ?>
                            <button type="submit" class="px-2 py-0.5 rounded bg-red-50 text-red-600 hover:bg-red-100">Khóa</button>
                        <?php else: ?>
                            <button type="submit" class="px-2 py-0.5 rounded bg-green-50 text-green-600 hover:bg-green-100">Mở</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="px-4 py-4 text-center text-gray-500">Không có tài khoản nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($taiKhoans->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\admin\taikhoan\index.blade.php ENDPATH**/ ?>