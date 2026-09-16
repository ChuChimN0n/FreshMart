<?php $__env->startSection('title', 'Admin Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold">Dashboard Quản trị</h1>
    <p class="text-gray-600">Xin chào, <?php echo e(Auth::user()->hoTen); ?></p>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm text-gray-500">Tổng sản phẩm</h3>
        <p class="text-2xl font-bold text-bhx-600"><?php echo e($tongSanPham); ?></p>
        <p class="text-xs text-gray-400">Còn hàng: <?php echo e($sanPhamConHang); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm text-gray-500">Tổng đơn hàng</h3>
        <p class="text-2xl font-bold text-blue-600"><?php echo e($tongDonHang); ?></p>
        <p class="text-xs text-gray-400">Chờ xác nhận: <?php echo e($donChoXacNhan); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm text-gray-500">Tổng khách hàng</h3>
        <p class="text-2xl font-bold text-purple-600"><?php echo e($tongKhachHang); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 col-span-2 md:col-span-3">
        <h3 class="text-sm text-gray-500">Doanh thu</h3>
        <p class="text-2xl font-bold text-orange-600"><?php echo e(number_format($doanhThu, 0, ',', '.')); ?>đ</p>
        <p class="text-xs text-gray-400">(Không tính đơn đã hủy)</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-bold mb-3">Đơn hàng mới nhất</h3>
        <?php $__empty_1 = true; $__currentLoopData = $donHangMoiNhat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex justify-between items-center py-2 border-b text-sm">
            <div>
                <span class="font-medium">#<?php echo e($dh->maDH); ?></span>
                <span class="text-gray-500"><?php echo e($dh->taiKhoan->hoTen ?? ''); ?></span>
            </div>
            <span class="px-2 py-1 rounded text-xs <?php echo e($dh->trangThaiBadge); ?>">
                <?php echo e($dh->trangThaiLabel); ?>

            </span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-gray-500 text-sm">Chưa có đơn hàng</p>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-bold mb-3">Quản lý nhanh</h3>
        <div class="space-y-2">
            <a href="<?php echo e(route('admin.baocao.index')); ?>" class="block bg-orange-50 p-3 rounded hover:bg-orange-100 text-sm font-medium text-orange-700">Xem báo cáo thống kê</a>
            <a href="<?php echo e(route('admin.nhacungcap.index')); ?>" class="block bg-bhx-50 p-3 rounded hover:bg-bhx-100 text-sm font-medium text-bhx-700">Quản lý nhà cung cấp</a>
            <a href="<?php echo e(route('admin.taikhoan.index')); ?>" class="block bg-blue-50 p-3 rounded hover:bg-blue-100 text-sm font-medium text-blue-700">Quản lý tài khoản</a>
            <a href="<?php echo e(route('admin.vaitro.index')); ?>" class="block bg-purple-50 p-3 rounded hover:bg-purple-100 text-sm font-medium text-purple-700">Quản lý vai trò & quyền</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>