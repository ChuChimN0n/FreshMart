<?php $__env->startSection('title', 'Chi tiết đơn #' . $donHang->maDH); ?>
<?php $__env->startSection('content'); ?>
<a href="<?php echo e(route('staff.donhang.index')); ?>" class="text-bhx-600 hover:underline mb-4 inline-block">← Quay lại danh sách</a>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="font-bold mb-3">Thông tin đơn hàng</h2>
        <p><strong>Mã đơn:</strong> #<?php echo e($donHang->maDH); ?></p>
        <p><strong>Khách hàng:</strong> <?php echo e($donHang->taiKhoan->hoTen ?? ''); ?></p>
        <p><strong>Ngày đặt:</strong> <?php echo e($donHang->ngayDat->format('d/m/Y H:i')); ?></p>
        <p><strong>Trạng thái:</strong>
            <span class="px-2 py-1 rounded text-xs font-medium <?php echo e($donHang->trangThaiBadge); ?>">
                <?php echo e($donHang->trangThaiLabel); ?>

            </span>
        </p>
        <hr class="my-3">
        <p><strong>Người nhận:</strong> <?php echo e($donHang->tenNguoiNhan); ?></p>
        <p><strong>Số ĐT:</strong> <?php echo e($donHang->soDienThoai); ?></p>
        <p><strong>Địa chỉ:</strong> <?php echo e($donHang->diaChi); ?></p>
        <p class="mt-3 text-lg font-bold text-bhx-600">Tổng: <?php echo e(number_format($donHang->tongTien, 0, ',', '.')); ?>đ</p>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="font-bold mb-3">Chi tiết sản phẩm</h2>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Sản phẩm</th>
                        <th class="px-3 py-2 text-right">Đơn giá</th>
                        <th class="px-3 py-2 text-center">SL</th>
                        <th class="px-3 py-2 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $donHang->chiTietDonHangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-t">
                        <td class="px-3 py-2"><?php echo e($ct->sanPham->tenSP ?? 'SP đã xóa'); ?></td>
                        <td class="px-3 py-2 text-right"><?php echo e(number_format($ct->donGia, 0, ',', '.')); ?>đ</td>
                        <td class="px-3 py-2 text-center"><?php echo e($ct->soLuong); ?></td>
                        <td class="px-3 py-2 text-right"><?php echo e(number_format($ct->thanhTien, 0, ',', '.')); ?>đ</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($donHang->trangThai != \App\Models\DonHang::HOAN_THANH && $donHang->trangThai != \App\Models\DonHang::DA_HUY): ?>
        <div class="bg-white rounded-lg shadow p-4 mt-4">
            <h2 class="font-bold mb-3">Cập nhật trạng thái</h2>
            <form method="POST" action="<?php echo e(route('staff.donhang.update', $donHang)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <select name="trangThai" class="w-full border rounded px-3 py-2 mb-3">
                    <?php $__currentLoopData = \App\Models\DonHang::VALID_TRANSITIONS[$donHang->trangThai] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $next): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($next); ?>"><?php echo e(\App\Models\DonHang::TRANG_THAI[$next]); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cập nhật</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\nhanvien\donhang\detail.blade.php ENDPATH**/ ?>