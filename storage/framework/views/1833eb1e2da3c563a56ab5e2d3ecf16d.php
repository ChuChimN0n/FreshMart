<?php $__env->startSection('title', 'Quản lý đơn hàng'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-4">Quản lý đơn hàng</h1>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm đơn hàng..." class="border rounded px-3 py-2 flex-1">
    <select name="trangThai" class="border rounded px-3 py-2">
        <option value="">-- Tất cả --</option>
        <?php $__currentLoopData = \App\Models\DonHang::TRANG_THAI; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>" <?php echo e(request('trangThai') == $key ? 'selected' : ''); ?>><?php echo e($val); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left">Mã đơn</th>
                <th class="px-4 py-3 text-left">Khách hàng</th>
                <th class="px-4 py-3 text-left">Người nhận</th>
                <th class="px-4 py-3 text-left">Ngày đặt</th>
                <th class="px-4 py-3 text-right">Tổng tiền</th>
                <th class="px-4 py-3 text-left">Trạng thái</th>
                <th class="px-4 py-3 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $donHangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t">
                <td class="px-4 py-3 font-medium">#<?php echo e($dh->maDH); ?></td>
                <td class="px-4 py-3"><?php echo e($dh->taiKhoan->hoTen ?? ''); ?></td>
                <td class="px-4 py-3"><?php echo e($dh->tenNguoiNhan); ?></td>
                <td class="px-4 py-3"><?php echo e($dh->ngayDat->format('d/m/Y')); ?></td>
                <td class="px-4 py-3 text-right"><?php echo e(number_format($dh->tongTien, 0, ',', '.')); ?>đ</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs font-medium <?php echo e($dh->trangThaiBadge); ?>">
                        <?php echo e($dh->trangThaiLabel); ?>

                    </span>
                </td>
                <td class="px-4 py-3">
                    <a href="<?php echo e(route('staff.donhang.detail', $dh)); ?>" class="text-blue-600 hover:underline">Chi tiết</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Không có đơn hàng nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($donHangs->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\nhanvien\donhang\index.blade.php ENDPATH**/ ?>