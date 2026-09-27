<?php

namespace App\Console\Commands;

use App\Models\DonHang;
use App\Services\CodeGenerator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:gan-ma-don-hang')]
#[Description('Sinh mã đơn cho các đơn hàng chưa có mã (chạy lại an toàn)')]
class BackfillOrderCodes extends Command
{
    public function handle(): int
    {
        $query = DonHang::whereNull('maDon')->orWhere('maDon', '')->orderBy('maDH');
        $total = (clone $query)->count();
        if ($total === 0) {
            $this->info('Mọi đơn hàng đã có mã đơn.');

            return self::SUCCESS;
        }

        $done = 0;
        $query->chunkById(200, function ($orders) use (&$done): void {
            foreach ($orders as $order) {
                $order->update(['maDon' => CodeGenerator::next('donhang', ['date' => $order->ngayDat])]);
                $done++;
            }
        }, 'maDH');

        $this->info("Đã gán mã cho {$done} đơn hàng.");

        return self::SUCCESS;
    }
}
