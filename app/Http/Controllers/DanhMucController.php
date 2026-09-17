<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DanhMucController extends Controller
{
    public function index(Request $request)
    {
        $query = DanhMuc::query();
        if ($request->filled('search')) {
            $query->where('tenDM', 'like', '%'.$request->search.'%');
        }
        $danhMucs = $query->orderBy('maDM', 'desc')->paginate(10);

        return view('staff.danhmuc.index', compact('danhMucs'));
    }

    public function create()
    {
        return view('staff.danhmuc.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenDM' => 'required|string|max:100|unique:DanhMuc,tenDM',
        ], [
            'tenDM.unique' => 'Tên Danh Mục đã tồn tại',
        ]);

        DanhMuc::create(['tenDM' => $request->tenDM]);
        Cache::forget('danhMucs');

        return redirect()->route('staff.danhmuc.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit(DanhMuc $danhmuc)
    {
        return view('staff.danhmuc.edit', ['danhMuc' => $danhmuc]);
    }

    public function update(Request $request, DanhMuc $danhmuc)
    {
        $request->validate([
            'tenDM' => 'required|string|max:100|unique:DanhMuc,tenDM,maDM,maDM',
        ]);

        $danhmuc->update(['tenDM' => $request->tenDM]);
        Cache::forget('danhMucs');

        return redirect()->route('staff.danhmuc.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(DanhMuc $danhmuc): RedirectResponse
    {
        if ($danhmuc->sanPhams()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục vì còn sản phẩm thuộc danh mục này!');
        }

        try {
            $danhmuc->delete();
        } catch (QueryException $e) {
            if ($danhmuc->sanPhams()->exists()) {
                return back()->with('error', 'Không thể xóa danh mục vì còn sản phẩm thuộc danh mục này!');
            }

            throw $e;
        }
        Cache::forget('danhMucs');

        return back()->with('success', 'Xóa danh mục thành công!');
    }
}
