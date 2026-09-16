<?php $__env->startSection('title', 'Đặt hàng'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
    <i class="bi bi-bag-check text-bhx-500"></i> Xác nhận đặt hàng
</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bhx-card p-5">
        <h2 class="font-bold text-gray-800 mb-3"><i class="bi bi-basket text-bhx-500"></i> Sản phẩm trong giỏ</h2>
        <?php $__currentLoopData = $gioHang->chiTietGioHangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex justify-between items-center py-2.5 border-b hover:bg-bhx-50/50 px-2 rounded">
            <div>
                <p class="font-medium text-sm"><?php echo e($ct->sanPham->tenSP); ?></p>
                <p class="text-xs text-gray-500"><?php echo e(number_format($ct->donGia, 0, ',', '.')); ?>đ x <?php echo e($ct->soLuong); ?> <?php echo e($ct->sanPham->donVi ?? 'kg'); ?></p>
            </div>
            <span class="font-medium text-sm"><?php echo e(number_format($ct->thanhTien, 0, ',', '.')); ?>đ</span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="mt-4 text-right">
            <span class="text-sm text-gray-500">Tổng tiền: </span>
            <span class="text-xl font-extrabold text-bhx-orange"><?php echo e(number_format($gioHang->tongTien, 0, ',', '.')); ?>đ</span>
        </div>
    </div>

    <div class="bhx-card p-5">
        <form method="POST" action="<?php echo e(route('donhang.place')); ?>">
            <?php echo csrf_field(); ?>
            <h2 class="font-bold text-gray-800 mb-3"><i class="bi bi-truck text-bhx-500"></i> Thông tin giao hàng</h2>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên người nhận *</label>
                <input type="text" name="tenNguoiNhan" value="<?php echo e(Auth::user()->hoTen); ?>" class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none <?php $__errorArgs = ['tenNguoiNhan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <?php $__errorArgs = ['tenNguoiNhan'];
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
                <input type="text" name="soDienThoai" value="<?php echo e(Auth::user()->soDienThoai); ?>" class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none <?php $__errorArgs = ['soDienThoai'];
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
                <input type="text" name="diaChi" value="<?php echo e(Auth::user()->diaChi); ?>" class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none <?php $__errorArgs = ['diaChi'];
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
                <button type="submit" class="bhx-btn-orange"><i class="bi bi-check2-square"></i> Đặt hàng</button>
                <a href="<?php echo e(route('giohang.index')); ?>" class="bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Quay lại giỏ</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\donhang\checkout.blade.php ENDPATH**/ ?>