<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class DanhMucController extends Controller
{
    public function index(Request $request)
    {
        $query = DanhMuc::withCount('sanPhams');
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
            'tenDM' => [
                'required',
                'string',
                'max:100',
                Rule::unique('DanhMuc', 'tenDM')->ignore($danhmuc->maDM, 'maDM'),
            ],
        ], [
            'tenDM.unique' => 'Tên Danh mục đã tồn tại',
        ]);

        $danhmuc->update(['tenDM' => $request->tenDM]);
        Cache::forget('danhMucs');

        return redirect()->route('staff.danhmuc.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(DanhMuc $danhmuc): RedirectResponse
    {
        if ($danhmuc->sanPhams()->exists()) {
            return back()->with('error', 'Danh mục này đã được sử dụng trong sản phẩm, không thể xóa!');
        }

        try {
            $danhmuc->delete();
        } catch (QueryException $e) {
            if ($danhmuc->sanPhams()->exists()) {
                return back()->with('error', 'Danh mục này đã được sử dụng trong sản phẩm, không thể xóa!');
            }

            throw $e;
        }
        Cache::forget('danhMucs');

        return back()->with('success', 'Xóa danh mục thành công!');
    }
}
