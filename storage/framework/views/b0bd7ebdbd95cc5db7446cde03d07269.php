<?php $__env->startSection('title', 'Thống kê sản phẩm'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('admin.baocao.index')); ?>" class="text-bhx-600 hover:underline text-sm">← Quay lại báo cáo</a>
    <h1 class="text-2xl font-bold mt-2">Thống kê sản phẩm</h1>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
            <input type="date" name="tuNgay" value="<?php echo e($tuNgay); ?>" class="border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
            <input type="date" name="denNgay" value="<?php echo e($denNgay); ?>" class="border rounded px-3 py-2" required>
        </div>
        <button type="submit" class="bg-bhx-500 text-white px-6 py-2 rounded hover:bg-bhx-600 font-medium">Thống kê</button>
    </form>
</div>

<?php if($errors->any()): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php echo e($errors->first()); ?>

    </div>
<?php endif; ?>

<div class="grid grid-cols-2 md:grid-cols-<?php echo e($tongBanTheoDonVi->count() + 1); ?> gap-4 mb-6">
    <?php $__currentLoopData = $tongBanTheoDonVi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donVi => $soLuong): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-sm text-gray-500">Tổng bán (<?php echo e($donVi); ?>)</div>
        <div class="text-2xl font-bold text-blue-600"><?php echo e(number_format($soLuong)); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-sm text-gray-500">Tổng tiền</div>
        <div class="text-2xl font-bold text-bhx-600"><?php echo e(number_format($tongTien, 0, ',', '.')); ?>đ</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-bhx-50">
            <tr>
                <th class="px-4 py-3 text-center">STT</th>
                <th class="px-4 py-3 text-left">Tên sản phẩm</th>
                <th class="px-4 py-3 text-center">Đơn vị</th>
                <th class="px-4 py-3 text-right">Đơn giá</th>
                <th class="px-4 py-3 text-center">Số lượng bán</th>
                <th class="px-4 py-3 text-right">Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 text-center"><?php echo e($index + 1); ?></td>
                <td class="px-4 py-3 font-medium"><?php echo e($item->tenSP); ?></td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium"><?php echo e($item->donVi); ?></span>
                </td>
                <td class="px-4 py-3 text-right"><?php echo e(number_format($item->giaBan, 0, ',', '.')); ?>đ</td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium"><?php echo e(number_format($item->tongBan)); ?> <?php echo e($item->donVi); ?></span>
                </td>
                <td class="px-4 py-3 text-right font-medium text-bhx-600"><?php echo e(number_format($item->tongTien, 0, ',', '.')); ?>đ</td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    <div class="text-3xl mb-2">📊</div>
                    Không có dữ liệu thống kê trong khoảng thời gian này
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
        <?php if($data->count()): ?>
        <tfoot class="bg-bhx-50 font-bold">
            <tr>
                <td colspan="4" class="px-4 py-3 text-right">Tổng cộng</td>
                <td class="px-4 py-3 text-center text-bhx-600">
                    <?php $__currentLoopData = $tongBanTheoDonVi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donVi => $soLuong): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e(number_format($soLuong)); ?> <?php echo e($donVi); ?><?php if(!$loop->last): ?>, <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </td>
                <td class="px-4 py-3 text-right text-bhx-600"><?php echo e(number_format($tongTien, 0, ',', '.')); ?>đ</td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\admin\baocao\sanpham.blade.php ENDPATH**/ ?>