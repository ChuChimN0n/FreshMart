<?php

namespace App\Console\Commands;

use App\Models\NhaCungCap;
use App\Services\CodeGenerator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:gan-ma-ncc')]
#[Description('Sinh mã NCC cho các nhà cung cấp chưa có mã (chạy lại an toàn)')]
class BackfillSupplierCodes extends Command
{
    public function handle(): int
    {
        $query = NhaCungCap::whereNull('codeNCC')->orWhere('codeNCC', '')->orderBy('maNCC');
        $total = (clone $query)->count();
        if ($total === 0) {
            $this->info('Mọi nhà cung cấp đã có mã NCC.');

            return self::SUCCESS;
        }

        $done = 0;
        $query->chunkById(200, function ($suppliers) use (&$done): void {
            foreach ($suppliers as $supplier) {
                $supplier->update(['codeNCC' => CodeGenerator::next('nhacungcap')]);
                $done++;
            }
        }, 'maNCC');

        $this->info("Đã gán mã cho {$done} nhà cung cấp.");

        return self::SUCCESS;
    }
}
