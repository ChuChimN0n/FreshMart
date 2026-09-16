<?php $__env->startSection('title', 'Giỏ hàng'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
    <i class="bi bi-cart3 text-bhx-500"></i> Giỏ hàng
</h1>

<?php if($gioHang && $gioHang->chiTietGioHangs->count() > 0): ?>
<div class="bhx-card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-bhx-600 text-white">
            <tr>
                <th class="px-4 py-3 text-left">Sản phẩm</th>
                <th class="px-4 py-3 text-right">Đơn giá</th>
                <th class="px-4 py-3 text-center">Số lượng</th>
                <th class="px-4 py-3 text-right">Thành tiền</th>
                <th class="px-4 py-3 text-center">Xóa</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $gioHang->chiTietGioHangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-t hover:bg-bhx-50/50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <?php if($ct->sanPham->hinhAnh): ?>
                            <img src="<?php echo e(asset('storage/'.$ct->sanPham->hinhAnh)); ?>" class="w-14 h-14 object-cover rounded-lg">
                        <?php else: ?>
                            <div class="w-14 h-14 bg-bhx-50 flex items-center justify-center rounded-lg text-bhx-300 text-xl"><i class="bi bi-basket"></i></div>
                        <?php endif; ?>
                        <div>
                            <p class="font-medium text-gray-800"><?php echo e($ct->sanPham->tenSP ?? 'SP đã xóa'); ?></p>
                            <?php if($ct->sanPham): ?>
                                <span class="text-xs text-gray-400"><?php echo e($ct->sanPham->danhMuc->tenDM ?? ''); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-right"><?php echo e(number_format($ct->donGia, 0, ',', '.')); ?>đ</td>
                <td class="px-4 py-3">
                    <form method="POST" action="<?php echo e(route('giohang.update', $ct)); ?>" class="flex items-center justify-center gap-1">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <input type="number" name="soLuong" value="<?php echo e($ct->soLuong); ?>" min="1" class="border rounded-lg w-16 text-center px-1 py-1.5 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none">
                        <button type="submit" class="text-bhx-600 hover:underline text-xs font-medium">Cập nhật</button>
                    </form>
                </td>
                <td class="px-4 py-3 text-right bhx-price"><?php echo e(number_format($ct->thanhTien, 0, ',', '.')); ?>đ</td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="<?php echo e(route('giohang.remove', $ct)); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-bhx-red hover:underline text-sm" onclick="return confirm('Xóa sản phẩm này?')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot class="bg-bhx-50">
            <tr>
                <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700">Tổng tiền:</td>
                <td class="px-4 py-3 text-right font-extrabold text-bhx-orange text-lg"><?php echo e(number_format($gioHang->tongTien, 0, ',', '.')); ?>đ</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="mt-4 flex justify-between items-center">
    <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-1.5 bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
        <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
    </a>
    <a href="<?php echo e(route('checkout')); ?>" class="bhx-btn-orange !px-8">
        Đặt hàng <i class="bi bi-arrow-right"></i>
    </a>
</div>
<?php else: ?>
<div class="bhx-card text-center py-16">
    <div class="text-6xl mb-4 text-bhx-300"><i class="bi bi-cart-x"></i></div>
    <p class="text-gray-500 text-lg mb-4">Giỏ hàng trống</p>
    <a href="<?php echo e(route('home')); ?>" class="bhx-btn-primary">Mua sắm ngay</a>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\giohang\index.blade.php ENDPATH**/ ?>