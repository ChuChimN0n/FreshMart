<?php

namespace App\Http\Controllers;

use App\Models\Quyen;
use App\Models\VaiTro;
use Illuminate\Http\Request;

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
            'quyens' => 'nullable|array',
        ]);

        $vaiTro = VaiTro::create([
            'tenVT' => $request->tenVT,
            'moTa' => $request->moTa,
        ]);

        if ($request->has('quyens')) {
            $vaiTro->quyens()->sync($request->quyens);
        }

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
            'quyens' => 'nullable|array',
        ]);

        $vaitro->update([
            'tenVT' => $request->tenVT,
            'moTa' => $request->moTa,
        ]);

        $vaitro->quyens()->sync($request->quyens ?? []);

        return redirect()->route('admin.vaitro.index')->with('success', 'Cập nhật vai trò thành công!');
    }
}
