<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private function store()
    {
        return Auth::user()->store;
    }

    public function index(Request $request)
    {
        $orders = $this->store()->orders()
            ->with('user','items')
            ->when($request->status, fn($q) => $q->where('status',$request->status))
            ->latest()
            ->paginate(20);

        return view('seller.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->store_id !== $this->store()->id, 403);
        $order->load('user','items.product.images','paymentLogs');
        return view('seller.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        abort_if($order->store_id !== $this->store()->id, 403);

        $request->validate([
            'status'          => 'required|string',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        // Seller hanya boleh ubah ke processing atau shipped
        $allowed = ['processing', 'shipped'];
        if (!in_array($request->status, $allowed)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $updates = ['status' => $request->status];

        if ($request->filled('tracking_number')) {
            $updates['tracking_number'] = $request->tracking_number;
        }
        if ($request->status === 'shipped') {
            $updates['shipped_at'] = now();
        }

        $order->update($updates);

        return redirect()->back()->with('success','Status pesanan diperbarui!');
    }
}