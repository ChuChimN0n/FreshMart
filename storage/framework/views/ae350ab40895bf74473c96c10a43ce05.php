<?php $__env->startSection('title', $taiKhoan->isCustomer() ? 'Thông tin tài khoản' : 'Sửa tài khoản'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-4"><?php echo e($taiKhoan->isCustomer() ? 'Thông tin tài khoản' : 'Sửa tài khoản'); ?></h1>
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <?php if($taiKhoan->isCustomer()): ?>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                <input type="text" value="<?php echo e($taiKhoan->hoTen); ?>" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                <input type="text" value="<?php echo e($taiKhoan->tenDangNhap); ?>" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value="<?php echo e($taiKhoan->email); ?>" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                <input type="text" value="<?php echo e($taiKhoan->soDienThoai); ?>" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <input type="text" value="<?php echo e($taiKhoan->diaChi); ?>" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                <input type="text" value="<?php echo e($taiKhoan->vaiTro->tenVT ?? ''); ?>" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <input type="text" value="<?php echo e($taiKhoan->trangThai == 'HOAT_DONG' ? 'Hoạt động' : 'Khóa'); ?>" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.taikhoan.index')); ?>" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Quay lại</a>
        </div>
    <?php else: ?>
        
        <form method="POST" action="<?php echo e(route('admin.taikhoan.update', $taiKhoan)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                    <input type="text" name="hoTen" value="<?php echo e(old('hoTen', $taiKhoan->hoTen)); ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <input type="text" value="<?php echo e($taiKhoan->tenDangNhap); ?>" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $taiKhoan->email)); ?>" class="w-full border rounded px-3 py-2 <?php $__errorArgs = ['email'];
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
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                    <input type="text" name="soDienThoai" value="<?php echo e(old('soDienThoai', $taiKhoan->soDienThoai)); ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <input type="text" name="diaChi" value="<?php echo e(old('diaChi', $taiKhoan->diaChi)); ?>" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò *</label>
                    <select name="maVT" class="w-full border rounded px-3 py-2" required>
                        <?php $__currentLoopData = $vaiTros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($vt->maVT); ?>" <?php echo e(old('maVT', $taiKhoan->maVT) == $vt->maVT ? 'selected' : ''); ?>><?php echo e($vt->tenVT); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới (để trống nếu không đổi)</label>
                    <div class="pw-wrap">
                        <input type="password" name="matKhau" class="w-full border rounded px-3 py-2 pr-10">
                        <span class="pw-toggle" onclick="togglePw(this)">👁</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái *</label>
                    <select name="trangThai" class="w-full border rounded px-3 py-2" required>
                        <option value="HOAT_DONG" <?php echo e(old('trangThai', $taiKhoan->trangThai) == 'HOAT_DONG' ? 'selected' : ''); ?>>Hoạt động</option>
                        <option value="KHOA" <?php echo e(old('trangThai', $taiKhoan->trangThai) == 'KHOA' ? 'selected' : ''); ?>>Khóa</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">Cập nhật</button>
                <a href="<?php echo e(route('admin.taikhoan.index')); ?>" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Hủy</a>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\admin\taikhoan\edit.blade.php ENDPATH**/ ?>