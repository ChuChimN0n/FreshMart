<?php $__env->startSection('title', 'Đăng nhập'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-md mx-auto">
    <div class="rounded-2xl overflow-hidden shadow-md">
        <div class="bg-bhx-700 px-6 py-5 text-center">
            <span class="w-10 h-10 mx-auto rounded-lg bg-white text-bhx-600 flex items-center justify-center mb-2"><i class="bi bi-basket text-xl"></i></span>
            <h1 class="text-lg font-extrabold text-white tracking-wide">BÁCH HÓA XANH</h1>
            <p class="text-bhx-100 text-xs mt-1">Đăng nhập để mua sắm và theo dõi đơn hàng</p>
        </div>

        <div class="bg-white p-6">
            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <input type="text" name="tenDangNhap" value="<?php echo e(old('tenDangNhap')); ?>"
                           class="w-full border rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-bhx-500 <?php $__errorArgs = ['tenDangNhap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           required autofocus>
                    <?php $__errorArgs = ['tenDangNhap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                    <div class="pw-wrap">
                        <input type="password" name="matKhau"
                               class="w-full border rounded-lg px-3 py-2.5 pr-10 focus:outline-none focus:ring-2 focus:ring-bhx-500 <?php $__errorArgs = ['matKhau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required>
                        <span class="pw-toggle" onclick="togglePw(this)">👁</span>
                    </div>
                    <?php $__errorArgs = ['matKhau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2 accent-bhx-500">
                    <label for="remember" class="text-sm text-gray-600">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="w-full bhx-btn-primary !py-3">
                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                </button>

                <p class="text-center mt-4 text-sm text-gray-600">
                    Chưa có tài khoản? <a href="<?php echo e(route('register')); ?>" class="text-bhx-600 hover:underline font-medium">Đăng ký ngay</a>
                </p>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\auth\login.blade.php ENDPATH**/ ?>