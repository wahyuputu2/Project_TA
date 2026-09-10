<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with('store.user')
            ->latest()->paginate(20);
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function process(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'action' => 'required|in:processed,rejected',
            'notes'  => 'nullable|string',
        ]);

        if ($request->action === 'rejected') {
            // Kembalikan saldo ke seller
            $withdrawal->store->addBalance($withdrawal->amount);
        }

        $withdrawal->update([
            'status'       => $request->action,
            'notes'        => $request->notes,
            'processed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Withdrawal berhasil diproses.');
    }
}