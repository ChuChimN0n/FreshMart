<?php

namespace App\Console\Commands;

use App\Models\SanPham;
use App\Services\CodeGenerator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:gan-ma-san-pham')]
#[Description('Sinh SKU cho các sản phẩm chưa có mã (chạy lại an toàn)')]
class BackfillProductSku extends Command
{
    public function handle(): int
    {
        $query = SanPham::whereNull('sku')->orWhere('sku', '')->orderBy('maSP');
        $total = (clone $query)->count();
        if ($total === 0) {
            $this->info('Mọi sản phẩm đã có SKU.');

            return self::SUCCESS;
        }

        $done = 0;
        $query->chunkById(200, function ($products) use (&$done): void {
            foreach ($products as $product) {
                $product->update(['sku' => CodeGenerator::next('sanpham', ['maDM' => $product->maDM])]);
                $done++;
            }
        }, 'maSP');

        $this->info("Đã gán SKU cho {$done} sản phẩm.");

        return self::SUCCESS;
    }
}
