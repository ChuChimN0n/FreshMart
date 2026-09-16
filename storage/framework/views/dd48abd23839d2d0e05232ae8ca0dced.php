<?php $__env->startSection('title', 'Sửa vai trò'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-4">Sửa vai trò</h1>
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="<?php echo e(route('admin.vaitro.update', $vaiTro)); ?>">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên vai trò *</label>
            <input type="text" name="tenVT" value="<?php echo e(old('tenVT', $vaiTro->tenVT)); ?>" class="w-full border rounded px-3 py-2 <?php $__errorArgs = ['tenVT'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
            <?php $__errorArgs = ['tenVT'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <input type="text" name="moTa" value="<?php echo e(old('moTa', $vaiTro->moTa)); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quyền</label>
            <div class="grid grid-cols-2 gap-2">
                <?php $currentQuyens = old('quyens', $vaiTro->quyens->pluck('maQuyen')->toArray()); ?>
                <?php $__currentLoopData = $quyens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="quyens[]" value="<?php echo e($q->maQuyen); ?>" <?php echo e(in_array($q->maQuyen, $currentQuyens) ? 'checked' : ''); ?>>
                    <span class="text-sm"><?php echo e($q->tenQuyen); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">Cập nhật</button>
            <a href="<?php echo e(route('admin.vaitro.index')); ?>" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Hủy</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\admin\vaitro\edit.blade.php ENDPATH**/ ?>