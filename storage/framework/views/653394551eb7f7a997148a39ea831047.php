<?php $__env->startSection('title', 'Quản lý vai trò'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Quản lý vai trò & quyền</h1>
    <a href="<?php echo e(route('admin.vaitro.create')); ?>" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">+ Thêm vai trò</a>
</div>

<div class="space-y-4">
    <?php $__currentLoopData = $vaiTros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold text-lg"><?php echo e($vt->tenVT); ?></h3>
                <p class="text-sm text-gray-500"><?php echo e($vt->moTa); ?></p>
            </div>
            <a href="<?php echo e(route('admin.vaitro.edit', $vt)); ?>" class="text-blue-600 hover:underline text-sm">Sửa</a>
        </div>
        <div class="mt-3 flex flex-wrap gap-2">
            <?php $__empty_1 = true; $__currentLoopData = $vt->quyens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <span class="bg-bhx-100 text-bhx-700 px-2 py-1 rounded text-xs"><?php echo e($q->tenQuyen); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <span class="text-gray-400 text-xs">Chưa có quyền</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\admin\vaitro\index.blade.php ENDPATH**/ ?>