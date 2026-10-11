<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar semua pesanan (read-only, untuk pengawasan).
     * Admin TIDAK memproses pesanan — itu tanggung jawab Seller.
     */
    public function index(Request $request)
    {
        $orders = Order::with('user', 'items', 'store')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where(fn($x) =>
                $x->where('order_number', 'like', '%'.$request->search.'%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%'.$request->search.'%'))
            ))
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Tampilkan detail pesanan (read-only).
     * Admin hanya memantau — perubahan status dilakukan oleh Seller.
     */
    public function show(Order $order)
    {
        $order->load('user', 'items.product.images', 'paymentLogs', 'store');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Hapus pesanan secara permanen (fungsi administratif sistem).
     * Digunakan hanya untuk membersihkan data bermasalah/uji coba.
     */
    public function destroy(Order $order)
    {
        $order->load('items.product', 'reviews');

        // Lepas lock produk
        $order->items->each(function ($item) {
            if ($item->product && $item->product->status === 'locked') {
                $item->product->unlock();
            }
        });

        // Hapus foto review
        foreach ($order->reviews as $review) {
            if ($review->photos) {
                foreach ($review->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }
        }

        // Hapus komisi terkait
        Commission::where('order_id', $order->id)->delete();

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil dihapus permanen.');
    }
}