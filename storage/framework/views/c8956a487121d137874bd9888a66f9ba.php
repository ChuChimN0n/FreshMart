<?php $__env->startSection('title', 'Đơn hàng của tôi'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
    <i class="bi bi-receipt text-bhx-500"></i> Đơn hàng của tôi
</h1>

<div class="bhx-card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-bhx-600 text-white">
            <tr>
                <th class="px-4 py-3 text-left">Mã đơn</th>
                <th class="px-4 py-3 text-left">Ngày đặt</th>
                <th class="px-4 py-3 text-right">Tổng tiền</th>
                <th class="px-4 py-3 text-left">Trạng thái</th>
                <th class="px-4 py-3 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $donHangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t hover:bg-bhx-50/50">
                <td class="px-4 py-3 font-semibold text-bhx-700">#<?php echo e($dh->maDH); ?></td>
                <td class="px-4 py-3"><?php echo e($dh->ngayDat->format('d/m/Y H:i')); ?></td>
                <td class="px-4 py-3 text-right bhx-price"><?php echo e(number_format($dh->tongTien, 0, ',', '.')); ?>đ</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs font-medium <?php echo e($dh->trangThaiBadge); ?>">
                        <?php echo e($dh->trangThaiLabel); ?>

                    </span>
                </td>
                <td class="px-4 py-3 flex gap-4">
                    <a href="<?php echo e(route('donhang.detail', $dh)); ?>" class="text-bhx-600 hover:underline font-medium"><i class="bi bi-eye"></i> Chi tiết</a>
                    <?php if($dh->canCancel()): ?>
                    <form method="POST" action="<?php echo e(route('donhang.cancel', $dh)); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="text-bhx-red hover:underline" onclick="return confirm('Bạn muốn hủy đơn này?')"><i class="bi bi-x-circle"></i> Hủy</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Chưa có đơn hàng nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($donHangs->links()); ?></div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\donhang\index.blade.php ENDPATH**/ ?>