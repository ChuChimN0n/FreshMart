<?php

namespace App\Http\Controllers;

use App\Models\Quyen;
use App\Models\VaiTro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VaiTroController extends Controller
{
    public function index()
    {
        $vaiTros = VaiTro::with('quyens')->get();

        return view('admin.vaitro.index', compact('vaiTros'));
    }

    public function create()
    {
        $quyens = Quyen::orderBy('tenQuyen')->get();

        return view('admin.vaitro.create', compact('quyens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenVT' => 'required|string|max:100|unique:VaiTro,tenVT',
            'moTa' => 'nullable|string|max:255',
            'quyens' => 'required|array|min:1',
            'quyens.*' => 'integer|distinct|exists:Quyen,maQuyen',
        ], [
            'tenVT.required' => 'Tên vai trò không được để trống',
            'tenVT.unique' => 'Tên vai trò này đã tồn tại, vui lòng chọn tên khác.',
            'quyens.required' => 'Vui lòng chọn ít nhất một quyền cho vai trò.',
            'quyens.min' => 'Vui lòng chọn ít nhất một quyền cho vai trò.',
        ]);

        $this->guardSinglePermissionGroup($request->input('quyens', []));

        DB::transaction(function () use ($request): void {
            $vaiTro = VaiTro::create($request->only('tenVT', 'moTa'));
            $vaiTro->quyens()->sync($request->input('quyens', []));
        });

        return redirect()->route('admin.vaitro.index')->with('success', 'Tạo vai trò thành công!');
    }

    public function edit(VaiTro $vaitro)
    {
        $vaitro->load('quyens');
        $quyens = Quyen::orderBy('tenQuyen')->get();

        return view('admin.vaitro.edit', ['vaiTro' => $vaitro, 'quyens' => $quyens]);
    }

    public function update(Request $request, VaiTro $vaitro)
    {
        $request->validate([
            'tenVT' => 'required|string|max:100|unique:VaiTro,tenVT,'.$vaitro->maVT.',maVT',
            'moTa' => 'nullable|string|max:255',
            'quyens' => [
                $vaitro->maVT === VaiTro::ADMIN_ID ? 'nullable' : 'required',
                'array',
                'min:1',
            ],
            'quyens.*' => 'integer|distinct|exists:Quyen,maQuyen',
        ], [
            'tenVT.required' => 'Tên vai trò không được để trống',
            'tenVT.unique' => 'Tên vai trò này đã tồn tại, vui lòng chọn tên khác.',
            'quyens.required' => 'Vui lòng chọn ít nhất một quyền cho vai trò.',
            'quyens.min' => 'Vui lòng chọn ít nhất một quyền cho vai trò.',
        ]);

        $this->guardSinglePermissionGroup($request->input('quyens', []));

        DB::transaction(function () use ($request, $vaitro): void {
            $vaitro->update($request->only('tenVT', 'moTa'));
            if ($vaitro->maVT !== VaiTro::ADMIN_ID) {
                $vaitro->quyens()->sync($request->input('quyens', []));
            }
        });

        return redirect()->route('admin.vaitro.index')->with('success', 'Cập nhật vai trò thành công!');
    }

    private function guardSinglePermissionGroup(array $maQuyens): void
    {
        if ($maQuyens === []) {
            return;
        }

        $names = Quyen::whereIn('maQuyen', $maQuyens)->pluck('tenQuyen')->all();
        $hasCustomer = array_intersect($names, Quyen::CUSTOMER_PERMISSIONS) !== [];
        $hasManagement = array_intersect($names, Quyen::MANAGEMENT_PERMISSIONS) !== [];

        if ($hasCustomer && $hasManagement) {
            throw ValidationException::withMessages([
                'quyens' => 'Một vai trò chỉ thuộc một nhóm: Khách hàng hoặc Quản trị/Nhân viên, không được trộn lẫn.',
            ]);
        }
    }

    public function destroy(VaiTro $vaitro): RedirectResponse
    {
        if (in_array($vaitro->maVT, [VaiTro::ADMIN_ID, VaiTro::STAFF_ID, VaiTro::KHACH_HANG_ID], true)) {
            return back()->with('error', 'Không thể xóa vai trò hệ thống!');
        }

        if ($vaitro->taiKhoans()->exists()) {
            return back()->with('error', 'Vai trò còn tài khoản đang sử dụng, không thể xóa!');
        }

        DB::transaction(function () use ($vaitro): void {
            $vaitro->quyens()->detach();
            $vaitro->delete();
        });

        return redirect()->route('admin.vaitro.index')->with('success', 'Xóa vai trò thành công!');
    }
}
