<?php $__env->startSection('title', 'Quản lý nhà cung cấp'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Quản lý nhà cung cấp</h1>
    <a href="<?php echo e(route('admin.nhacungcap.create')); ?>" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">+ Thêm NCC</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm NCC..." class="border rounded px-3 py-2 flex-1">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Mã NCC</th>
                <th class="px-4 py-2 text-left">Tên NCC</th>
                <th class="px-4 py-2 text-left">Số ĐT</th>
                <th class="px-4 py-2 text-left">Email</th>
                <th class="px-4 py-2 text-left">Địa chỉ</th>
                <th class="px-4 py-2 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $nhaCungCaps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ncc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo e($ncc->maNCC); ?></td>
                <td class="px-4 py-2"><?php echo e($ncc->tenNCC); ?></td>
                <td class="px-4 py-2"><?php echo e($ncc->soDienThoai); ?></td>
                <td class="px-4 py-2"><?php echo e($ncc->email); ?></td>
                <td class="px-4 py-2"><?php echo e($ncc->diaChi); ?></td>
                <td class="px-4 py-2">
                    <a href="<?php echo e(route('admin.nhacungcap.edit', $ncc)); ?>" class="text-blue-600 hover:underline">Sửa</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">Không có NCC nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($nhaCungCaps->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\admin\nhacungcap\index.blade.php ENDPATH**/ ?>