<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\Quyen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BaoCaoController extends Controller
{
    public function index()
    {
        return view('admin.baocao.index');
    }

    public function sanPham(Request $request)
    {
        [$tuNgay, $denNgay] = $this->validateRange($request);
        $result = $this->sanPhamData($tuNgay, $denNgay);

        return view('admin.baocao.sanpham', compact('tuNgay', 'denNgay') + $result);
    }

    public function doanhThu(Request $request)
    {
        [$tuNgay, $denNgay] = $this->validateRange($request);
        $result = $this->doanhThuData($tuNgay, $denNgay);

        return view('admin.baocao.doanhthu', compact('tuNgay', 'denNgay') + $result);
    }

    public function donHang(Request $request)
    {
        [$tuNgay, $denNgay] = $this->validateRange($request);
        $result = $this->donHangData($tuNgay, $denNgay);

        return view('admin.baocao.donhang', compact('tuNgay', 'denNgay') + $result);
    }

    public function nhapHang(Request $request)
    {
        [$tuNgay, $denNgay] = $this->validateRange($request);
        $result = $this->nhapHangData($tuNgay, $denNgay);

        return view('admin.baocao.nhaphang', compact('tuNgay', 'denNgay') + $result);
    }

    public function tonKho()
    {
        $result = $this->tonKhoData();

        return view('admin.baocao.tonkho', $result);
    }

    public function exportSanPham(Request $request): Response|RedirectResponse
    {
        $this->requireExportPermission(Quyen::PRODUCT_REPORT);
        [$tuNgay, $denNgay] = $this->validateRange($request);
        $result = $this->tryFetchData(fn (): array => $this->sanPhamData($tuNgay, $denNgay));
        if ($result instanceof RedirectResponse) {
            return $result;
        }

        if ($result['data']->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu để xuất báo cáo.');
        }

        $rows = $result['data']->map(fn ($item): array => [
            $item->tenSP, $item->donVi, $item->giaBan, $item->tongBan, $item->tongTien,
        ])->all();
        $rows[] = ['Tổng cộng', '', '', $result['data']->sum('tongBan'), $result['tongTien']];

        return $this->exportXlsx(
            'BÁO CÁO SẢN PHẨM',
            "Từ {$tuNgay} đến {$denNgay} - Xuất lúc ".now()->format('H:i d/m/Y'),
            "bao-cao-san-pham-{$tuNgay}-{$denNgay}.xlsx",
            ['Tên sản phẩm', 'Đơn vị tính', 'Đơn giá (đ)', 'Số lượng bán', 'Thành tiền (đ)'],
            $rows,
            ['left', 'center', 'right', 'center', 'right']
        );
    }

    public function exportDoanhThu(Request $request): Response|RedirectResponse
    {
        $this->requireExportPermission(Quyen::REVENUE_REPORT);
        [$tuNgay, $denNgay] = $this->validateRange($request);
        $result = $this->tryFetchData(fn (): array => $this->doanhThuData($tuNgay, $denNgay));
        if ($result instanceof RedirectResponse) {
            return $result;
        }

        if ($result['data']->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu để xuất báo cáo.');
        }

        $rows = $result['data']->map(fn ($item): array => [$item->ngay, $item->soDon, $item->doanhThu])->all();
        $rows[] = ['Tổng cộng', $result['tongSoDon'], $result['tongDoanhThu']];

        return $this->exportXlsx(
            'BÁO CÁO DOANH THU',
            "Từ {$tuNgay} đến {$denNgay} - Xuất lúc ".now()->format('H:i d/m/Y'),
            "bao-cao-doanh-thu-{$tuNgay}-{$denNgay}.xlsx",
            ['Ngày', 'Số đơn hàng', 'Doanh thu (đ)'],
            $rows,
            ['center', 'center', 'right']
        );
    }

    public function exportDonHang(Request $request): Response|RedirectResponse
    {
        $this->requireExportPermission(Quyen::ORDER_REPORT);
        [$tuNgay, $denNgay] = $this->validateRange($request);
        $result = $this->tryFetchData(fn (): array => $this->donHangData($tuNgay, $denNgay));
        if ($result instanceof RedirectResponse) {
            return $result;
        }

        if ($result['data']->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu để xuất báo cáo.');
        }

        $rows = $result['data']->map(fn ($item): array => [$item->label, $item->soLuong, $item->tongTien])->all();
        $rows[] = ['Tổng cộng', $result['tongDon'], $result['tongTien']];

        return $this->exportXlsx(
            'BÁO CÁO ĐƠN HÀNG',
            "Từ {$tuNgay} đến {$denNgay} - Xuất lúc ".now()->format('H:i d/m/Y'),
            "bao-cao-don-hang-{$tuNgay}-{$denNgay}.xlsx",
            ['Trạng thái đơn hàng', 'Số đơn hàng', 'Tổng tiền (đ)'],
            $rows,
            ['left', 'center', 'right']
        );
    }

    public function exportNhapHang(Request $request): Response|RedirectResponse
    {
        $this->requireExportPermission(Quyen::NHAP_HANG_REPORT);
        [$tuNgay, $denNgay] = $this->validateRange($request);
        $result = $this->tryFetchData(fn (): array => $this->nhapHangData($tuNgay, $denNgay));
        if ($result instanceof RedirectResponse) {
            return $result;
        }

        if ($result['data']->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu để xuất báo cáo.');
        }

        $rows = $result['data']->map(fn ($item): array => [
            $item->maPhieu, $item->tenNCC, $item->tenSP, $item->soLuong, $item->giaNhap, $item->thanhTien,
        ])->all();
        $rows[] = ['Tổng cộng', '', '', $result['tongSL'], '', $result['tongTien']];

        return $this->exportXlsx(
            'BÁO CÁO NHẬP HÀNG',
            "Từ {$tuNgay} đến {$denNgay} - Xuất lúc ".now()->format('H:i d/m/Y'),
            "bao-cao-nhap-hang-{$tuNgay}-{$denNgay}.xlsx",
            ['Mã phiếu nhập', 'Nhà cung cấp', 'Tên sản phẩm', 'Số lượng nhập', 'Giá nhập (đ)', 'Thành tiền (đ)'],
            $rows,
            ['left', 'left', 'left', 'center', 'right', 'right']
        );
    }

    public function exportTonKho(): Response|RedirectResponse
    {
        $this->requireExportPermission(Quyen::TON_KHO_REPORT);
        $result = $this->tryFetchData(fn (): array => $this->tonKhoData());
        if ($result instanceof RedirectResponse) {
            return $result;
        }

        if ($result['data']->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu để xuất báo cáo.');
        }

        $labels = ['HET_HANG' => 'Hết hàng', 'SAP_HET_HANG' => 'Sắp hết hàng', 'CON_HANG' => 'Còn hàng'];
        $rows = $result['data']->map(fn ($item): array => [
            $item->tenSP, $item->donVi, $item->soLuongTon, $item->mucTonToiThieu, $labels[$item->trangThaiTon] ?? $item->trangThaiTon,
        ])->all();

        return $this->exportXlsx(
            'BÁO CÁO TỒN KHO',
            'Tồn kho hiện tại - Xuất lúc '.now()->format('H:i d/m/Y'),
            'bao-cao-ton-kho-'.now()->format('Ymd-His').'.xlsx',
            ['Tên sản phẩm', 'Đơn vị tính', 'Số lượng tồn', 'Mức tồn tối thiểu', 'Trạng thái tồn kho'],
            $rows,
            ['left', 'center', 'center', 'center', 'left']
        );
    }

    /**
     * @return array{string, string}
     */
    private function validateRange(Request $request): array
    {
        // Sidebar submenu không kèm khoảng ngày: mặc định 30 ngày gần nhất (giống thẻ hub).
        $request->merge([
            'tuNgay' => $request->input('tuNgay') ?: now()->subDays(30)->format('Y-m-d'),
            'denNgay' => $request->input('denNgay') ?: now()->format('Y-m-d'),
        ]);

        $request->validate(
            [
                'tuNgay' => 'required|date',
                'denNgay' => 'required|date|after_or_equal:tuNgay',
            ],
            [
                'tuNgay.required' => 'Vui lòng chọn ngày bắt đầu.',
                'tuNgay.date' => 'Ngày bắt đầu không đúng định dạng.',
                'denNgay.required' => 'Vui lòng chọn ngày kết thúc.',
                'denNgay.date' => 'Ngày kết thúc không đúng định dạng.',
                'denNgay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu. Vui lòng chọn lại ngày.',
            ],
            [
                'tuNgay' => 'ngày bắt đầu',
                'denNgay' => 'ngày kết thúc',
            ]
        );

        return [$request->tuNgay, $request->denNgay];
    }

    private function requireExportPermission(string $viewPermission): void
    {
        $user = Auth::user();
        abort_unless(
            $user && $user->hasPermission(Quyen::XUAT_BAO_CAO) && $user->hasPermission($viewPermission),
            403,
            'Bạn không có quyền xuất báo cáo'
        );
    }

    /**
     * Lấy số liệu xuất file; lỗi truy xuất thì về trang trước kèm thông báo.
     *
     * @return array<string, mixed>|RedirectResponse
     */
    private function tryFetchData(callable $fetch): array|RedirectResponse
    {
        try {
            return $fetch();
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Không thể tải dữ liệu báo cáo. Vui lòng thử lại.');
        }
    }

    private function sanPhamData(string $tuNgay, string $denNgay): array
    {
        $data = ChiTietDonHang::select(
            'SanPham.maSP',
            'SanPham.tenSP',
            'SanPham.donVi',
            'SanPham.giaBan',
            DB::raw('SUM(ChiTietDonHang.soLuong) AS tongBan'),
            DB::raw('SUM(ChiTietDonHang.thanhTien) AS tongTien')
        )
            ->join('DonHang', 'ChiTietDonHang.maDH', '=', 'DonHang.maDH')
            ->join('SanPham', 'ChiTietDonHang.maSP', '=', 'SanPham.maSP')
            ->whereIn('DonHang.trangThai', [DonHang::DA_XAC_NHAN, DonHang::DANG_GIAO, DonHang::HOAN_THANH])
            ->whereDate('DonHang.ngayDat', '>=', $tuNgay)
            ->whereDate('DonHang.ngayDat', '<=', $denNgay)
            ->groupBy('SanPham.maSP', 'SanPham.tenSP', 'SanPham.donVi', 'SanPham.giaBan')
            ->orderByDesc('tongBan')
            ->get();

        return [
            'data' => $data,
            'tongBanTheoDonVi' => $data->groupBy('donVi')->map(fn ($g) => $g->sum('tongBan')),
            'tongTien' => $data->sum('tongTien'),
        ];
    }

    private function doanhThuData(string $tuNgay, string $denNgay): array
    {
        $data = DonHang::select(
            DB::raw('DATE(ngayDat) AS ngay'),
            DB::raw('COUNT(*) AS soDon'),
            DB::raw('SUM(tongTien) AS doanhThu')
        )
            ->whereIn('trangThai', [DonHang::DA_XAC_NHAN, DonHang::DANG_GIAO, DonHang::HOAN_THANH])
            ->whereDate('ngayDat', '>=', $tuNgay)
            ->whereDate('ngayDat', '<=', $denNgay)
            ->groupBy(DB::raw('DATE(ngayDat)'))
            ->orderBy('ngay')
            ->get();

        return [
            'data' => $data,
            'tongDoanhThu' => $data->sum('doanhThu'),
            'tongSoDon' => $data->sum('soDon'),
        ];
    }

    private function donHangData(string $tuNgay, string $denNgay): array
    {
        $data = DonHang::select(
            'trangThai',
            DB::raw('COUNT(*) AS soLuong'),
            DB::raw('SUM(tongTien) AS tongTien')
        )
            ->whereDate('ngayDat', '>=', $tuNgay)
            ->whereDate('ngayDat', '<=', $denNgay)
            ->groupBy('trangThai')
            ->get()
            ->map(function ($item) {
                $item->label = DonHang::TRANG_THAI[$item->trangThai] ?? $item->trangThai;

                return $item;
            });

        return [
            'data' => $data,
            'tongDon' => $data->sum('soLuong'),
            'tongTien' => $data->sum('tongTien'),
        ];
    }

    private function nhapHangData(string $tuNgay, string $denNgay): array
    {
        // Chỉ phiếu DA_XAC_NHAN (view đã lọc sẵn).
        $data = DB::table('vw_baocao_nhaphang')
            ->whereDate('ngayTao', '>=', $tuNgay)
            ->whereDate('ngayTao', '<=', $denNgay)
            ->orderBy('ngayTao', 'desc')
            ->get();

        return [
            'data' => $data,
            'tongPhieu' => $data->pluck('maPN')->unique()->count(),
            'tongSL' => $data->sum('soLuong'),
            'tongTien' => $data->sum('thanhTien'),
            'theoNCC' => $data->groupBy('maNCC')->map(fn ($rows) => [
                'tenNCC' => $rows->first()->tenNCC,
                'soPhieu' => $rows->pluck('maPN')->unique()->count(),
                'tongSL' => $rows->sum('soLuong'),
                'tongTien' => $rows->sum('thanhTien'),
            ])->values(),
        ];
    }

    private function tonKhoData(): array
    {
        $data = DB::table('vw_baocao_tonkho')->orderBy('maSP')->get();

        return [
            'data' => $data,
            'hetHang' => $data->where('trangThaiTon', 'HET_HANG')->count(),
            'sapHet' => $data->where('trangThaiTon', 'SAP_HET_HANG')->count(),
            'conHang' => $data->where('trangThaiTon', 'CON_HANG')->count(),
        ];
    }

    /**
     * Xuất file Excel có định dạng: tiêu đề gộp, header tô màu, kẻ bảng, tổng đậm.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     * @param  array<int, string>  $aligns  Căn lề từng cột: left|center|right
     */
    private function exportXlsx(string $title, string $subtitle, string $filename, array $headers, array $rows, array $aligns = []): Response|RedirectResponse
    {
        try {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(mb_substr($title, 0, 31));
            $colCount = count($headers);
            $lastCol = Coordinate::stringFromColumnIndex($colCount);

            $sheet->mergeCells("A1:{$lastCol}1");
            $sheet->setCellValue('A1', $title);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells("A2:{$lastCol}2");
            $sheet->setCellValue('A2', $subtitle);
            $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11)->getColor()->setARGB('FF6B7280');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $headerRow = 4;
            foreach ($headers as $i => $header) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1).$headerRow, $header);
            }
            $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($headerRow)->setRowHeight(24);

            $rowNum = $headerRow;
            foreach ($rows as $row) {
                $rowNum++;
                foreach ($row as $i => $value) {
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1).$rowNum, $value);
                }
            }

            // Dòng tổng (dòng cuối) in đậm: các view báo cáo đều để tổng ở cuối.
            $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->getFont()->setBold(true);

            $sheet->getStyle("A{$headerRow}:{$lastCol}{$rowNum}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
            ]);
            foreach ($aligns as $i => $align) {
                $col = Coordinate::stringFromColumnIndex($i + 1);
                $sheet->getStyle("{$col}{$headerRow}:{$col}{$rowNum}")->getAlignment()
                    ->setHorizontal(match ($align) {
                        'center' => Alignment::HORIZONTAL_CENTER,
                        'right' => Alignment::HORIZONTAL_RIGHT,
                        default => Alignment::HORIZONTAL_LEFT,
                    });
            }
            for ($i = 1; $i <= $colCount; $i++) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
            }
            $sheet->freezePane('A5');
            $sheet->setAutoFilter("A{$headerRow}:{$lastCol}{$rowNum}");

            $temp = tempnam(sys_get_temp_dir(), 'baocao');
            if ($temp === false || ! is_writable($temp)) {
                throw new \RuntimeException('Không thể tạo tệp tạm để xuất báo cáo.');
            }

            try {
                (new Xlsx($spreadsheet))->save($temp);
                $spreadsheet->disconnectWorksheets();
                $spreadsheet = null;

                $content = file_get_contents($temp);
                if ($content === false || $content === '') {
                    throw new \RuntimeException('Tệp báo cáo tạo ra bị rỗng.');
                }
            } finally {
                if (is_file($temp)) {
                    unlink($temp);
                }
            }

            return response($content, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Xuất báo cáo thất bại, vui lòng thử lại.');
        }
    }
}
