<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCap;
use Illuminate\Http\Request;

class NhaCungCapController extends Controller
{
    public function index(Request $request)
    {
        $query = NhaCungCap::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tenNCC', 'like', "%$search%")
                    ->orWhere('soDienThoai', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
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
            'soDienThoai' => 'required|string|max:15',
            'email' => 'nullable|email|max:100',
            'diaChi' => 'nullable|string|max:255',
        ]);

        NhaCungCap::create($request->only('tenNCC', 'soDienThoai', 'email', 'diaChi'));

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
            'soDienThoai' => 'required|string|max:15',
            'email' => 'nullable|email|max:100',
            'diaChi' => 'nullable|string|max:255',
        ]);

        $nhacungcap->update($request->only('tenNCC', 'soDienThoai', 'email', 'diaChi'));

        return redirect()->route('admin.nhacungcap.index')->with('success', 'Cập nhật nhà cung cấp thành công!');
    }
}
