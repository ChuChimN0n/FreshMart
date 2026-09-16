<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartComposer
{
    public function compose(View $view): void
    {
        $cartCount = 0;

        $user = Auth::user();
        if ($user && $user->gioHang) {
            $cartCount = $user->gioHang->chiTietGioHangs()->count();
        }

        $view->with('cartCount', $cartCount);
    }
}
