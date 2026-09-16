<?php $__env->startSection('title', 'Quản lý danh mục'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Quản lý danh mục</h1>
    <a href="<?php echo e(route('staff.danhmuc.create')); ?>" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">+ Thêm danh mục</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm danh mục..." class="border rounded px-3 py-2 flex-1">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Mã DM</th>
                <th class="px-4 py-2 text-left">Tên danh mục</th>
                <th class="px-4 py-2 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $danhMucs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo e($dm->maDM); ?></td>
                <td class="px-4 py-2"><?php echo e($dm->tenDM); ?></td>
                <td class="px-4 py-2">
                    <a href="<?php echo e(route('staff.danhmuc.edit', $dm)); ?>" class="text-blue-600 hover:underline">Sửa</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Không có danh mục nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($danhMucs->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\staff\danhmuc\index.blade.php ENDPATH**/ ?>