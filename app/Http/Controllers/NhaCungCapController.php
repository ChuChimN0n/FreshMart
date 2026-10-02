<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCap;
use App\Services\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NhaCungCapController extends Controller
{
    public function index(Request $request)
    {
        $query = NhaCungCap::withCount('sanPhams');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tenNCC', 'like', "%$search%")
                    ->orWhere('soDienThoai', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('codeNCC', 'like', "%$search%");
            });
        }
        $nhaCungCaps = $query->orderBy('maNCC', 'desc')->paginate(10);

        return view('admin.nhacungcap.index', compact('nhaCungCaps'));
    }

    public function create()
    {
        return view('admin.nhacungcap.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenNCC' => 'required|string|max:100',
            'soDienThoai' => [
                'required',
                'string',
                'unique:NhaCungCap,soDienThoai',
                'regex:/^(03|05|07|08|09)[0-9]{8}$/',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:100',
                'unique:NhaCungCap,email',
            ],
            'diaChi' => 'required|string|max:255',
        ], [
            'tenNCC.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'email.required' => 'Vui lòng nhập Email.',
            'soDienThoai.required' => 'Vui lòng nhập số điện thoại nhà cung cấp.',
            'diaChi.required' => 'Vui lòng nhập địa chỉ nhà cung cấp.',
            'email.email' => 'Địa chỉ email không hợp lệ hoặc không tồn tại.',
            'email.unique' => 'Email này đã được sử dụng.',
            'soDienThoai.unique' => 'Số điện thoại đã được sử dụng',
            'soDienThoai.regex' => 'Số điện thoại không đúng định dạng (phải gồm 10 chữ số hợp lệ tại Việt Nam).',
        ]);

        // Mã NCC luôn do hệ thống sinh; insertUnique tự sinh lại và retry khi đua trùng.
        try {
            CodeGenerator::insertUnique(
                fn () => NhaCungCap::create($request->only('tenNCC', 'soDienThoai', 'email', 'diaChi')
                    + ['codeNCC' => CodeGenerator::next('nhacungcap')]),
                'codeNCC'
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Chưa thể lưu nhà cung cấp. Vui lòng thử lại!');
        }

        return redirect()->route('admin.nhacungcap.index')->with('success', 'Thêm nhà cung cấp thành công!');
    }

    public function edit(NhaCungCap $nhacungcap)
    {
        return view('admin.nhacungcap.edit', ['nhaCungCap' => $nhacungcap]);
    }

    public function update(Request $request, NhaCungCap $nhacungcap)
    {
        $request->validate([
            'tenNCC' => 'required|string|max:100',
            'soDienThoai' => [
                'required',
                'string',
                Rule::unique('NhaCungCap', 'soDienThoai')->ignore($nhacungcap->maNCC, 'maNCC'),
                'regex:/^(03|05|07|08|09)[0-9]{8}$/',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:100',
                Rule::unique('NhaCungCap', 'email')->ignore($nhacungcap->maNCC, 'maNCC'),
            ],
            'diaChi' => 'nullable|string|max:255',
        ], [
            'tenNCC.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'email.required' => 'Vui lòng nhập Email.',
            'soDienThoai.required' => 'Vui lòng nhập số điện thoại nhà cung cấp.',
            'diaChi.required' => 'Vui lòng nhập địa chỉ nhà cung cấp.',
            'email.email' => 'Địa chỉ email không hợp lệ hoặc không tồn tại.',
            'email.unique' => 'Email này đã được sử dụng.',
            'soDienThoai.unique' => 'Số điện thoại đã được sử dụng',
            'soDienThoai.regex' => 'Số điện thoại không đúng định dạng (phải gồm 10 chữ số hợp lệ tại Việt Nam).',
        ]);

        $nhacungcap->update($request->only('tenNCC', 'soDienThoai', 'email', 'diaChi'));

        return redirect()->route('admin.nhacungcap.index')->with('success', 'Cập nhật nhà cung cấp thành công!');
    }

    public function destroy(NhaCungCap $nhacungcap): RedirectResponse
    {
        if ($nhacungcap->sanPhams()->exists() || $nhacungcap->cungCapSanPhams()->exists()) {
            return back()->with('error', 'Nhà cung cấp này đã được sử dụng trong sản phẩm, không thể xóa!');
        }

        if ($nhacungcap->phieuNhaps()->exists()) {
            return back()->with('error', 'Nhà cung cấp này đã phát sinh phiếu nhập, không thể xóa!');
        }

        try {
            $nhacungcap->delete();
        } catch (QueryException $e) {
            if ($nhacungcap->sanPhams()->exists() || $nhacungcap->phieuNhaps()->exists() || $nhacungcap->cungCapSanPhams()->exists()) {
                return back()->with('error', 'Nhà cung cấp này đã được sử dụng, không thể xóa!');
            }

            throw $e;
        }

        return back()->with('success', 'Xóa nhà cung cấp thành công!');
    }
}
