<?php

namespace App\Http\Controllers;

use App\Services\CodeGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuggestCodeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'profile' => 'required|string|in:sanpham,nhacungcap',
            'maDM' => 'nullable|exists:DanhMuc,maDM',
        ]);

        return response()->json([
            'code' => CodeGenerator::next($request->profile, $request->only('maDM')),
        ]);
    }
}
