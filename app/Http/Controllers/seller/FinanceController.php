<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\Commission;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    private function sellerStore(): ?Store
    {
        return Store::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->first();
    }

    public function index()
    {
        $store = $this->sellerStore();

        if (!$store) {
            return redirect()->route('home')
                ->with('error', 'Toko tidak ditemukan.');
        }

        $commissions  = Commission::where('store_id', $store->id)
                            ->with('order')
                            ->latest()
                            ->paginate(20);

        $withdrawals  = Withdrawal::where('store_id', $store->id)
                            ->latest()
                            ->paginate(10);

        $totalRevenue  = Commission::where('store_id', $store->id)
                            ->where('status', 'settled')
                            ->sum('seller_amount');

        $pendingBalance = Commission::where('store_id', $store->id)
                            ->where('status', 'pending')
                            ->sum('seller_amount');

        return view('seller.finance', compact(
            'store', 'commissions', 'withdrawals',
            'totalRevenue', 'pendingBalance'
        ));
    }

    public function withdraw(Request $request)
    {
        $store = $this->sellerStore();

        if (!$store) {
            return redirect()->route('home')->with('error', 'Toko tidak ditemukan.');
        }

        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:50000',
                'max:' . $store->balance,
            ],
        ]);

        Withdrawal::create([
            'store_id'            => $store->id,
            'amount'              => $request->amount,
            'bank_name'           => $store->bank_name,
            'bank_account_number' => $store->bank_account_number,
            'bank_account_name'   => $store->bank_account_name,
            'status'              => 'pending',
        ]);

        // Kurangi saldo
        $store->decrement('balance', $request->amount);

        return redirect()->back()
            ->with('success', 'Permintaan withdraw Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil dikirim! Diproses 1–3 hari kerja.');
    }
}