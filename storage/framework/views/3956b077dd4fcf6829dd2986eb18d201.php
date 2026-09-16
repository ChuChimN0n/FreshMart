<?php $__env->startSection('title', 'Quản lý sản phẩm'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Quản lý sản phẩm</h1>
    <a href="<?php echo e(route('staff.sanpham.create')); ?>" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">+ Thêm sản phẩm</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm sản phẩm..." class="border rounded px-3 py-2 flex-1">
    <select name="maDM" class="border rounded px-3 py-2">
        <option value="">-- Tất cả danh mục --</option>
        <?php $__currentLoopData = $danhMucs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($dm->maDM); ?>" <?php echo e(request('maDM') == $dm->maDM ? 'selected' : ''); ?>><?php echo e($dm->tenDM); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Mã SP</th>
                <th class="px-4 py-2 text-left">Tên sản phẩm</th>
                <th class="px-4 py-2 text-left">Danh mục</th>
                <th class="px-4 py-2 text-left">NCC</th>
                <th class="px-4 py-2 text-right">Giá</th>
                <th class="px-4 py-2 text-right">Tồn kho</th>
                <th class="px-4 py-2 text-left">Trạng thái</th>
                <th class="px-4 py-2 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $sanPhams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo e($sp->maSP); ?></td>
                <td class="px-4 py-2 font-medium"><?php echo e($sp->tenSP); ?></td>
                <td class="px-4 py-2"><?php echo e($sp->danhMuc->tenDM ?? ''); ?></td>
                <td class="px-4 py-2"><?php echo e($sp->nhaCungCap->tenNCC ?? ''); ?></td>
                <td class="px-4 py-2 text-right"><?php echo e(number_format($sp->giaBan, 0, ',', '.')); ?>đ</td>
                <td class="px-4 py-2 text-right"><?php echo e($sp->soLuong); ?> <?php echo e($sp->donVi); ?></td>
                <td class="px-4 py-2">
                    <?php if($sp->trangThai == \App\Models\SanPham::DANG_BAN): ?>
                        <span class="text-bhx-600">Đang bán</span>
                    <?php else: ?>
                        <span class="text-red-600">Ngừng bán</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-2">
                    <a href="<?php echo e(route('staff.sanpham.edit', $sp)); ?>" class="text-blue-600 hover:underline">Sửa</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="px-4 py-4 text-center text-gray-500">Không có sản phẩm nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($sanPhams->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\tools\laragon\www\laravel-FreshVege\resources\views\staff\sanpham\index.blade.php ENDPATH**/ ?>