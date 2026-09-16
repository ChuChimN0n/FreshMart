<?php $__env->startSection('title', 'Thông tin cá nhân'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Thông tin cá nhân</h1>

    <?php if(session('success')): ?>
        <div class="bg-bhx-50 border border-bhx-400 text-bhx-700 px-4 py-3 rounded mb-4">
            <i class="bi bi-check-circle-fill"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            ✗ <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="font-bold mb-4">Thông tin hiện tại</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Họ tên:</span>
                <p class="font-medium"><?php echo e($user->hoTen); ?></p>
            </div>
            <div>
                <span class="text-gray-500">Tên đăng nhập:</span>
                <p class="font-medium"><?php echo e($user->tenDangNhap); ?></p>
            </div>
            <div>
                <span class="text-gray-500">Email:</span>
                <p class="font-medium"><?php echo e($user->email); ?></p>
            </div>
            <div>
                <span class="text-gray-500">Số điện thoại:</span>
                <p class="font-medium"><?php echo e($user->soDienThoai); ?></p>
            </div>
            <div class="col-span-2">
                <span class="text-gray-500">Địa chỉ:</span>
                <p class="font-medium"><?php echo e($user->diaChi); ?></p>
            </div>
            <div>
                <span class="text-gray-500">Vai trò:</span>
                <p class="font-medium"><?php echo e($user->vaiTro->tenVT ?? ''); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-bold mb-4">Chỉnh sửa thông tin</h2>
        <form method="POST" action="<?php echo e(route('profile.update')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                <input type="text" name="hoTen" value="<?php echo e(old('hoTen', $user->hoTen)); ?>"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bhx-500 <?php $__errorArgs = ['hoTen'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <?php $__errorArgs = ['hoTen'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bhx-500 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                <input type="text" name="soDienThoai" value="<?php echo e(old('soDienThoai', $user->soDienThoai)); ?>"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bhx-500 <?php $__errorArgs = ['soDienThoai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <?php $__errorArgs = ['soDienThoai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ *</label>
                <input type="text" name="diaChi" value="<?php echo e(old('diaChi', $user->diaChi)); ?>"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bhx-500 <?php $__errorArgs = ['diaChi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <?php $__errorArgs = ['diaChi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Cập nhật</button>
                <a href="<?php echo e(route('change-password')); ?>" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Đổi mật khẩu</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\auth\profile.blade.php ENDPATH**/ ?>