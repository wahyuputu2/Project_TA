<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SellerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->role, ['seller', 'admin', 'superadmin'])) {
            return redirect()->route('seller.register')
                ->with('error', 'Kamu harus menjadi seller terlebih dahulu.');
        }

        // Ambil store berdasarkan user_id langsung (bukan relasi hasOne)
        $store = \App\Models\Store::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();

        if (!$store) {
            // Cek apakah ada store tapi pending
            $pendingStore = \App\Models\Store::where('user_id', $user->id)->first();
            if ($pendingStore) {
                return redirect()->route('home')
                    ->with('error', 'Toko kamu masih ' . $pendingStore->statusLabel() . '. Hubungi admin.');
            }
            return redirect()->route('seller.register')
                ->with('error', 'Kamu belum memiliki toko.');
        }

        // Simpan store ke request agar bisa diakses di controller
        $request->merge(['_seller_store' => $store]);

        return $next($request);
    }
}