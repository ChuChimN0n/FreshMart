<?php $__env->startSection('title', 'Chi tiết đơn #' . $donHang->maDH); ?>
<?php $__env->startSection('content'); ?>
<a href="<?php echo e(route('donhang.index')); ?>" class="inline-flex items-center gap-1.5 text-bhx-600 hover:underline mb-4 font-medium">
    <i class="bi bi-arrow-left"></i> Quay lại danh sách
</a>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bhx-card p-5">
        <h2 class="font-bold mb-3 text-gray-800"><i class="bi bi-info-circle text-bhx-500"></i> Thông tin đơn hàng</h2>
        <div class="space-y-2 text-sm">
            <p><strong>Mã đơn:</strong> <span class="font-semibold text-bhx-700">#<?php echo e($donHang->maDH); ?></span></p>
            <p><strong>Ngày đặt:</strong> <?php echo e($donHang->ngayDat->format('d/m/Y H:i')); ?></p>
            <p class="flex items-center gap-2"><strong>Trạng thái:</strong>
                <span class="px-2 py-1 rounded text-xs font-medium <?php echo e($donHang->trangThaiBadge); ?>">
                    <?php echo e($donHang->trangThaiLabel); ?>

                </span>
            </p>
        </div>
        <hr class="my-3">
        <div class="space-y-2 text-sm">
            <p><strong>Người nhận:</strong> <?php echo e($donHang->tenNguoiNhan); ?></p>
            <p><strong>Số ĐT:</strong> <?php echo e($donHang->soDienThoai); ?></p>
            <p><strong>Địa chỉ:</strong> <?php echo e($donHang->diaChi); ?></p>
        </div>
        <p class="mt-4 text-lg font-extrabold text-bhx-orange">Tổng: <?php echo e(number_format($donHang->tongTien, 0, ',', '.')); ?>đ</p>
    </div>

    <div class="bhx-card p-5">
        <h2 class="font-bold mb-3 text-gray-800"><i class="bi bi-bag-check text-bhx-500"></i> Chi tiết sản phẩm</h2>
        <table class="w-full text-sm">
            <thead class="bg-bhx-50 rounded-lg">
                <tr>
                    <th class="px-3 py-2 text-left">Sản phẩm</th>
                    <th class="px-3 py-2 text-right">Đơn giá</th>
                    <th class="px-3 py-2 text-center">SL</th>
                    <th class="px-3 py-2 text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $donHang->chiTietDonHangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t hover:bg-bhx-50/50">
                    <td class="px-3 py-2"><?php echo e($ct->sanPham->tenSP ?? 'SP đã xóa'); ?></td>
                    <td class="px-3 py-2 text-right"><?php echo e(number_format($ct->donGia, 0, ',', '.')); ?>đ</td>
                    <td class="px-3 py-2 text-center"><?php echo e($ct->soLuong); ?> <?php echo e($ct->sanPham->donVi ?? 'kg'); ?></td>
                    <td class="px-3 py-2 text-right bhx-price"><?php echo e(number_format($ct->thanhTien, 0, ',', '.')); ?>đ</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<?php if($donHang->trangThai == \App\Models\DonHang::HOAN_THANH && $donHang->chiTietDonHangs->count()): ?>
<div class="mt-6 bhx-card p-5">
    <h2 class="font-bold mb-4 text-gray-800"><i class="bi bi-star text-bhx-yellow"></i> Đánh giá sản phẩm</h2>
    <p class="text-sm text-gray-500 mb-4">Bạn có thể đánh giá từng sản phẩm trong đơn hàng này.</p>

    <?php $__currentLoopData = $donHang->chiTietDonHangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $spId = $ct->sanPham->maSP ?? null;
            $spTen = $ct->sanPham->tenSP ?? 'SP đã xóa';
            $daDanhGia = $daDanhGia->firstWhere('maSP', $spId);
        ?>
        <div class="border rounded-xl p-4 mb-3 <?php echo e($daDanhGia ? 'bg-bhx-50' : ''); ?>">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <span class="font-medium"><?php echo e($spTen); ?></span>
                    <span class="text-gray-500 text-sm ml-2">(<?php echo e($ct->soLuong); ?> <?php echo e($ct->sanPham->donVi ?? 'kg'); ?> × <?php echo e(number_format($ct->donGia, 0, ',', '.')); ?>đ)</span>
                </div>

                <?php if($daDanhGia): ?>
                    <span class="text-bhx-600 text-sm font-medium"><i class="bi bi-check-circle-fill"></i> Đã đánh giá <?php echo e(str_repeat('⭐', $daDanhGia->soSao)); ?></span>
                <?php else: ?>
                    <button type="button" onclick="toggleReviewForm(<?php echo e($spId); ?>)" class="bg-bhx-500 hover:bg-bhx-600 text-white px-3 py-1.5 rounded-lg text-sm transition" id="btn-review-<?php echo e($spId); ?>">Đánh giá</button>
                <?php endif; ?>
            </div>

            <?php if(!$daDanhGia): ?>
            <form method="POST" action="<?php echo e(route('danhgia.store')); ?>" class="mt-3 hidden" id="form-review-<?php echo e($spId); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="maSP" value="<?php echo e($spId); ?>">
                <div class="flex gap-1 mb-2">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="soSao" value="<?php echo e($i); ?>" <?php echo e($i == 5 ? 'checked' : ''); ?> class="hidden peer">
                        <span class="text-2xl peer-checked:text-yellow-400 text-gray-300">⭐</span>
                    </label>
                    <?php endfor; ?>
                </div>
                <textarea name="noiDung" rows="2" class="w-full border rounded-lg px-3 py-2 mb-2 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none" placeholder="Nhận xét về <?php echo e($spTen); ?>..." required></textarea>
                <div class="flex gap-2">
                    <button type="submit" class="bhx-btn-primary !py-2 !px-4 text-sm">Gửi đánh giá</button>
                    <button type="button" onclick="toggleReviewForm(<?php echo e($spId); ?>)" class="bg-gray-300 text-gray-700 px-4 py-1.5 rounded-lg text-sm hover:bg-gray-400">Hủy</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<script>
function toggleReviewForm(spId) {
    const form = document.getElementById('form-review-' + spId);
    form.classList.toggle('hidden');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\donhang\detail.blade.php ENDPATH**/ ?>