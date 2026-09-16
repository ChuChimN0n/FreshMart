<?php $__env->startSection('title', 'Quản lý đánh giá'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-4">Quản lý đánh giá</h1>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm theo tên sản phẩm..." class="border rounded px-3 py-2 flex-1">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left">Khách hàng</th>
                <th class="px-4 py-3 text-left">Sản phẩm</th>
                <th class="px-4 py-3 text-center">Số sao</th>
                <th class="px-4 py-3 text-left">Nội dung</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $danhGias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t">
                <td class="px-4 py-3"><?php echo e($dg->taiKhoan->hoTen ?? ''); ?></td>
                <td class="px-4 py-3"><?php echo e($dg->sanPham->tenSP ?? ''); ?></td>
                <td class="px-4 py-3 text-center text-yellow-500"><?php echo e(str_repeat('⭐', $dg->soSao)); ?></td>
                <td class="px-4 py-3 max-w-xs truncate"><?php echo e($dg->noiDung); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Không có đánh giá nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($danhGias->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\nhanvien\danhgia\index.blade.php ENDPATH**/ ?>