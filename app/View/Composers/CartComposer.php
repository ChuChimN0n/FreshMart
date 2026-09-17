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
        if ($user && $user->isCustomer() && $user->gioHang) {
            $cartCount = $user->gioHang->chiTietGioHangs()->count();
        }

        $view->with('cartCount', $cartCount);
        $view->with('managementLinks', collect(config('navigation.management', []))
            ->filter(fn (array $item): bool => $user
                && $user->canAccessRoute($item['route'])
                && ! ($user->isAdmin() && $item['route'] === 'staff.dashboard'))
            ->values());
    }
}
