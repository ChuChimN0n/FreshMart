<?php

namespace Database\Seeders;

use App\Models\ChiTietDonHang;
use App\Models\DanhGia;
use App\Models\DanhMuc;
use App\Models\DonHang;
use App\Models\NhaCungCap;
use App\Models\SanPham;
use App\Models\TaiKhoan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    private const NHOM_NCC = [
        ['NCC Thực Phẩm Đà Lạt', '0934567890', 'dalat@ncc.vn', 'Đà Lạt, Lâm Đồng'],
        ['NCC Trái Cây Miền Tây', '0934567891', 'mientay@ncc.vn', 'Cần Thơ'],
        ['NCC Rau Sạch Hưng Yên', '0934567892', 'hungyen@ncc.vn', 'Hưng Yên'],
        ['NCC Rau Củ An Toàn', '0934567893', 'antoan@ncc.vn', 'Vĩnh Long'],
        ['NCC Thịt Sạch Đồng Nai', '0934567894', 'thitsach@ncc.vn', 'Đồng Nai'],
        ['NCC Hải Sản Kiên Giang', '0934567895', 'haisan@ncc.vn', 'Kiên Giang'],
        ['NCC Lương Thực Việt', '0934567896', 'luongthuc@ncc.vn', 'An Giang'],
    ];

    private const NHOM_DM = [
        'Rau củ', 'Trái cây', 'Rau thơm',
        'Thịt heo', 'Thịt gà', 'Hải sản', 'Gạo & Ngũ cốc',
        'Trứng', 'Đồ khô & Gia vị', 'Bánh kẹo & Đồ uống',
    ];

    private const BO_VEGETABLES = [
        'Rau muống', 'Rau má', 'Cải thìa', 'Mồng tơi', 'Xà lách', 'Hành lá',
    ];

    /**
     * @var array<int, array{0: string, 1: string, 2: string, 3: int, 4: int, 5: int, 6: string, 7: string, 8: string}>
     */
    private const SAN_PHAMS = [
        // Rau củ (DM1)
        ['Cải thìa', 'NCC Thực Phẩm Đà Lạt', 'Rau củ', 18000, 60, 'Cải thìa non xanh, ngọt mềm, phù hợp nấu canh hoặc xào tỏi.', 'caithia', '🥬', 'kg'],
        ['Bông cải xanh', 'NCC Thực Phẩm Đà Lạt', 'Rau củ', 45000, 55, 'Bông cải xanh tươi, đầu bông dày, giàu chất xơ và vitamin.', 'bongcaixanh', '🥦', 'kg'],
        ['Cà tím', 'NCC Rau Sạch Hưng Yên', 'Rau củ', 28000, 70, 'Cà tím dài, vỏ bóng, thịt chắc, đắng nhẹ rất hợp kho tộ.', 'catim', '🍆', 'kg'],
        ['Bí đỏ', 'NCC Rau Củ An Toàn', 'Rau củ', 32000, 65, 'Bí đỏ ruột vàng ươm, ngọt bùi, dùng nấu soup hay làm bánh.', 'bido', '🎃', 'kg'],
        ['Củ dền', 'NCC Trái Cây Miền Tây', 'Rau củ', 25000, 60, 'Củ dền đỏ tươi, mọng nước, tốt cho máu, ép nước rất ngon.', 'cuden', '🍠', 'kg'],
        // Trái cây (DM2)
        ['Táo', 'NCC Trái Cây Miền Tây', 'Trái cây', 55000, 80, 'Táo giòn, ngọt thanh, ít chua, gọt vỏ xanh hoặc đỏ.', 'tao', '🍎', 'kg'],
        ['Cam sành', 'NCC Trái Cây Miền Tây', 'Trái cây', 38000, 90, 'Cam sành vỏ dày, múi mọng nước, ngọt đậm.', 'camsanh', '🍊', 'kg'],
        ['Xoài', 'NCC Trái Cây Miền Tây', 'Trái cây', 48000, 75, 'Xoài chín vàng, thơm, thịt dày, ít xơ.', 'xoai', '🥭', 'kg'],
        ['Nho', 'NCC Trái Cây Miền Tây', 'Trái cây', 88000, 50, 'Nho xanh ngọt giòn, từng chùm to, bảo quản lạnh kỹ lưỡng.', 'nho', '🍇', 'kg'],
        ['Dưa hấu', 'NCC Trái Cây Miền Tây', 'Trái cây', 30000, 85, 'Dưa hấu ruột đỏ, ngọt mát, giải nhiệt mùa hè.', 'duahau', '🍉', 'kg'],
        // Rau thơm (DM3)
        ['Xà lách', 'NCC Rau Sạch Hưng Yên', 'Rau thơm', 15000, 70, 'Xà lách tươi giòn, dùng ăn kèm hoặc làm salad.', 'xalach', '🥬', 'kg'],
        ['Mồng tơi', 'NCC Rau Củ An Toàn', 'Rau thơm', 12000, 80, 'Mồng tơi non, lá mềm, nấu canh cua rất ngọt.', 'mongtoi', '🥬', 'kg'],
        ['Hành lá', 'NCC Rau Củ An Toàn', 'Rau thơm', 10000, 90, 'Hành lá tươi xanh, thơm nồng, không thuốc trừ sâu.', 'hanhla', '🧅', 'kg'],
        // Thịt heo (DM4)
        ['Ba chỉ heo', 'NCC Thịt Sạch Đồng Nai', 'Thịt heo', 159000, 40, 'Ba chỉ heo tươi, tỷ lệ nạc mỡ cân bằng, phù hợp kho, ram.', 'bachino', '🥩', 'kg'],
        ['Sườn non heo', 'NCC Thịt Sạch Đồng Nai', 'Thịt heo', 165000, 35, 'Sườn non heo嫩, thịt ngọt, hầm hoặc nướng đều ngon.', 'suongnon', '🥩', 'kg'],
        ['Thịt nạc vai heo', 'NCC Thịt Sạch Đồng Nai', 'Thịt heo', 139000, 45, 'Thịt nạc vai mềm, ít gân, băm viên hoặc xào hành.', 'nacvai', '🥩', 'kg'],
        // Thịt gà (DM5)
        ['Gà ta nguyên con', 'NCC Thịt Sạch Đồng Nai', 'Thịt gà', 145000, 30, 'Gà ta thả vườn, thịt săn chắc, da giòn, luộc hoặc quay.', 'gata', '🍗', 'kg'],
        ['Đùi gà', 'NCC Thịt Sạch Đồng Nai', 'Thịt gà', 85000, 50, 'Đùi gà tươi, thịt dày, ướp nướng hoặc chiên giòn.', 'duiga', '🍗', 'kg'],
        ['Ức gà', 'NCC Thịt Sạch Đồng Nai', 'Thịt gà', 110000, 45, 'Ức gà không xương, thịt mềm, giàu protein, salad hoặc áp chảo.', 'ucga', '🍗', 'kg'],
        // Hải sản (DM6)
        ['Tôm sú', 'NCC Hải Sản Kiên Giang', 'Hải sản', 349000, 25, 'Tôm sú tươi sống, size lớn, thịt ngọt, chắc, hấp hoặc nướng.', 'tomsu', '🦐', 'kg'],
        ['Cá basa phi lê', 'NCC Hải Sản Kiên Giang', 'Hải sản', 99000, 40, 'Cá basa phi lê đông lạnh, thịt mềm, ít xương, chiên hoặc hấp.', 'cabasa', '🐟', 'kg'],
        ['Mực ống', 'NCC Hải Sản Kiên Giang', 'Hải sản', 189000, 30, 'Mực ống tươi, thịt dày, nhúng mắm hoặc nướng mọi.', 'muc', '🦑', 'kg'],
        // Gạo & Ngũ cốc (DM7)
        ['Gạo ST25', 'NCC Lương Thực Việt', 'Gạo & Ngũ cốc', 39000, 80, 'Gạo ST25 hạt dài, dẻo thơm, bậc nhất Việt Nam.', 'gaost25', '🍚', 'kg'],
        ['Gạo nếp cái hoa vàng', 'NCC Lương Thực Việt', 'Gạo & Ngũ cốc', 25000, 70, 'Gạo nếp cái hoa vàng, dẻo thơm, nấu xôi hoặc làm bánh chưng.', 'gaonep', '🍚', 'kg'],
        ['Đậu xanh', 'NCC Lương Thực Việt', 'Gạo & Ngũ cốc', 45000, 60, 'Đậu xanh hạt to, vỏ mỏng, nấu chè hoặc làm bánh.', 'dauxanh', '🫘', 'kg'],
        // Trứng (DM8)
        ['Trứng gà ta', 'NCC Thịt Sạch Đồng Nai', 'Trứng', 4500, 200, 'Trứng gà ta thả vườn, lòng đỏ đậm, nhiều dinh dưỡng.', 'trungga', '🥚', 'quả'],
        ['Trứng vịt', 'NCC Thịt Sạch Đồng Nai', 'Trứng', 5000, 150, 'Trứng vịt lớn, béo ngậy, thích hợp luộc hoặc làm trứng muối.', 'trungvit', '🥚', 'quả'],
        ['Trứng cút', 'NCC Thịt Sạch Đồng Nai', 'Trứng', 2500, 180, 'Trứng cút nhỏ, bùi, giàu đạm, rất tốt cho trẻ em.', 'trungcut', '🥚', 'quả'],
        // Đồ khô & Gia vị (DM9)
        ['Mì gói Hảo Hảo', 'NCC Lương Thực Việt', 'Đồ khô & Gia vị', 5000, 300, 'Mì gói vị tôm chua cay, sợi mì dai, nhanh gọn cho bữa ăn.', 'migoi', '🍜', 'gói'],
        ['Nước mắm Phú Quốc', 'NCC Lương Thực Việt', 'Đồ khô & Gia vị', 55000, 80, 'Nước mắm truyền thống Phú Quốc, đạm cao, thơm nồng.', 'nuocmam', '🫗', 'chai'],
        ['Dầu ăn Tường An', 'NCC Lương Thực Việt', 'Đồ khô & Gia vị', 80000, 60, 'Dầu ăn thực vật, không cholesterol, chiên xào thơm ngon.', 'dauan', '🫗', 'chai'],
        ['Đường trắng', 'NCC Lương Thực Việt', 'Đồ khô & Gia vị', 25000, 100, 'Đường trắng tinh luyện, hạt mịn, dùng pha chế hoặc nấu ăn.', 'duong', '🍬', 'gói'],
        // Bánh kẹo & Đồ uống (DM10)
        ['Bánh quy Oreo', 'NCC Lương Thực Việt', 'Bánh kẹo & Đồ uống', 25000, 120, 'Bánh quy kem socola, giòn tan, bữa ăn nhẹ yêu thích.', 'banhoreo', '🍪', 'hộp'],
        ['Sữa tươi Vinamilk', 'NCC Lương Thực Việt', 'Bánh kẹo & Đồ uống', 9000, 200, 'Sữa tươi tiệt trùng 100%, bổ sung canxi, béo ngậy.', 'suatuoi', '🥛', 'hộp'],
        ['Nước suối Lavie', 'NCC Lương Thực Việt', 'Bánh kẹo & Đồ uống', 8000, 250, 'Nước suối tinh khiết, đóng chai tiện lợi, giải khát nhanh.', 'nuocsuoi', '💧', 'chai'],
    ];

    /**
     * @var array<int, array{0: string, 1: int, 2: string}>
     */
    private const DANH_GIAS = [
        ['Cà rốt', 5, 'Cà rốt tươi, ngọt, vị rất ngon, đóng gói sạch sẽ.'],
        ['Rau muống', 5, 'Rau tươi ngon, không úa, giao nhanh. Sẽ mua lại!'],
        ['Rau má', 5, 'Rau má xanh tươi, ép nước uống rất mát.'],
        ['Cà chua', 4, 'Cà chua chín đều, ngọt, nấu canh rất thơm.'],
        ['Cải thìa', 5, 'Cải thìa non, sạch, xào tỏi rất ngọt.'],
        ['Bông cải xanh', 5, 'Bông cải to, tươi sạch, mua lần 2 rồi.'],
        ['Táo', 4, 'Táo giòn, ngọt thanh, giao đúng hẹn.'],
        ['Cam sành', 5, 'Cam nhiều nước, ngọt vừa phải, rất hài lòng.'],
        ['Xoài', 5, 'Xoài chín đều, thơm, ngọt, đóng gói cẩn thận.'],
        ['Xà lách', 4, 'Xà lách tươi giòn, đóng gói bảo quản tốt.'],
        ['Nho', 5, 'Nho ngọt, không bị dập, sẽ ủng hộ tiếp.'],
        ['Dưa hấu', 4, 'Dưa hấu ngọt mát, vỏ mỏng ruột đỏ.'],
    ];

    /**
     * @var array<int, array{0: int, 1: string, 2: string, 3: array<string, int>}>
     */
    private const DON_HANGS = [
        [0, '2026-09-10 10:15:00', 'CHO_XAC_NHAN', ['Cải thìa' => 2, 'Nho' => 1]],
        [1, '2026-09-11 09:30:00', 'DA_XAC_NHAN', ['Bông cải xanh' => 1, 'Táo' => 2]],
        [2, '2026-09-11 15:45:00', 'HOAN_THANH', ['Xà lách' => 3]],
        [0, '2026-09-12 11:20:00', 'DANG_GIAO', ['Cà tím' => 2, 'Dưa hấu' => 1]],
        [1, '2026-09-13 08:10:00', 'HOAN_THANH', ['Cam sành' => 2, 'Xoài' => 1]],
        [2, '2026-09-13 17:50:00', 'CHO_XAC_NHAN', ['Mồng tơi' => 2, 'Hành lá' => 1]],
        [0, '2026-09-14 14:05:00', 'DA_HUY', ['Bí đỏ' => 2]],
        [1, '2026-09-15 10:40:00', 'DANG_GIAO', ['Củ dền' => 1, 'Bông cải xanh' => 2]],
        [2, '2026-09-16 09:15:00', 'HOAN_THANH', ['Táo' => 1, 'Nho' => 1]],
        [0, '2026-09-16 11:30:00', 'CHO_XAC_NHAN', ['Xoài' => 2, 'Xà lách' => 1]],
    ];

    public function run(): void
    {
        $this->seedDanhMucs();
        $this->seedNhaCungCaps();
        $this->seedSanPhams();
        $this->seedDanhGias();
        $this->seedDonHangs();
    }

    private function seedDanhMucs(): void
    {
        foreach (self::NHOM_DM as $tenDM) {
            DanhMuc::firstOrCreate(['tenDM' => $tenDM]);
        }
    }

    private function seedNhaCungCaps(): void
    {
        foreach (self::NHOM_NCC as [$ten, $soDienThoai, $email, $diaChi]) {
            NhaCungCap::firstOrCreate(['tenNCC' => $ten], [
                'soDienThoai' => $soDienThoai,
                'email' => $email,
                'diaChi' => $diaChi,
            ]);
        }
    }

    private function seedSanPhams(): void
    {
        foreach (self::SAN_PHAMS as [$ten, $tenNCC, $tenDM, $giaBan, $soLuong, $moTa, $slug, $emoji, $donVi]) {
            $maNCC = NhaCungCap::where('tenNCC', $tenNCC)->value('maNCC');
            $maDM = DanhMuc::where('tenDM', $tenDM)->value('maDM');

            if (! $maNCC || ! $maDM) {
                $this->command->warn("Bỏ qua {$ten}: NCC={$tenNCC}({$maNCC}) DM={$tenDM}({$maDM}).");

                continue;
            }

            SanPham::firstOrCreate(['tenSP' => $ten], [
                'maNCC' => $maNCC,
                'maDM' => $maDM,
                'hinhAnh' => "sanpham/{$slug}.svg",
                'donVi' => $donVi,
                'giaBan' => $giaBan,
                'moTa' => $moTa,
                'soLuong' => $soLuong,
                'trangThai' => 'DANG_BAN',
            ]);

            $this->seedSanPhamImage($slug, $ten, $emoji);
        }

        foreach (self::BO_VEGETABLES as $ten) {
            SanPham::where('tenSP', $ten)->update(['donVi' => 'bó']);
        }
    }

    private function seedSanPhamImage(string $slug, string $tenSP, string $emoji): void
    {
        $filePath = Storage::disk('public')->path("sanpham/{$slug}.svg");

        if (file_exists($filePath)) {
            return;
        }

        $svg = <<<SVG
<svg width="400" height="300" xmlns="http://www.w3.org/2000/svg">
  <rect width="400" height="300" fill="#22c55e"/>
  <text x="200" y="130" font-size="80" text-anchor="middle" fill="white">{$emoji}</text>
  <text x="200" y="200" font-size="26" text-anchor="middle" fill="white" font-family="sans-serif" font-weight="bold">{$tenSP}</text>
</svg>
SVG;

        file_put_contents($filePath, $svg);
    }

    private function seedDanhGias(): void
    {
        $customerIds = TaiKhoan::where('maVT', 3)->orderBy('maTK')->pluck('maTK')->values();

        if ($customerIds->isEmpty()) {
            return;
        }

        foreach (self::DANH_GIAS as $index => [$tenSP, $soSao, $noiDung]) {
            $maSP = SanPham::where('tenSP', $tenSP)->value('maSP');

            if (! $maSP) {
                continue;
            }

            $maTK = $customerIds[$index % $customerIds->count()];

            DanhGia::firstOrCreate(['maTK' => $maTK, 'maSP' => $maSP], [
                'soSao' => $soSao,
                'noiDung' => $noiDung,
            ]);
        }
    }

    private function seedDonHangs(): void
    {
        $customers = TaiKhoan::where('maVT', 3)->orderBy('maTK')->get();

        if ($customers->isEmpty()) {
            return;
        }

        foreach (self::DON_HANGS as [$customerIndex, $ngayDat, $trangThai, $sanPhams]) {
            $customer = $customers[$customerIndex % $customers->count()];

            $tongTien = 0;
            $chiTietData = [];

            foreach ($sanPhams as $tenSP => $soLuong) {
                $sanPham = SanPham::where('tenSP', $tenSP)->first();

                if (! $sanPham) {
                    continue;
                }

                $thanhTien = $soLuong * $sanPham->giaBan;
                $tongTien += $thanhTien;

                $chiTietData[] = [
                    'maSP' => $sanPham->maSP,
                    'soLuong' => $soLuong,
                    'donGia' => $sanPham->giaBan,
                    'thanhTien' => $thanhTien,
                ];
            }

            if ($chiTietData === []) {
                continue;
            }

            $isSeeded = DonHang::where('maTK', $customer->maTK)
                ->where('ngayDat', $ngayDat)
                ->where('tongTien', $tongTien)
                ->exists();

            if ($isSeeded) {
                continue;
            }

            $donHang = DonHang::create([
                'maTK' => $customer->maTK,
                'ngayDat' => $ngayDat,
                'tenNguoiNhan' => $customer->hoTen,
                'soDienThoai' => $customer->soDienThoai ?: '0900000000',
                'diaChi' => $customer->diaChi ?: 'Hà Nội',
                'tongTien' => $tongTien,
                'trangThai' => $trangThai,
            ]);

            foreach ($chiTietData as $chiTiet) {
                $chiTiet['maDH'] = $donHang->maDH;
                ChiTietDonHang::create($chiTiet);
            }
        }
    }
}
