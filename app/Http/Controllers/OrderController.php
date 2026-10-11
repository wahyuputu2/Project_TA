<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        Order::cancelExpiredOrders();
        Product::releaseExpiredLocks();

        $orders = Order::with('items.product.images')
            ->where('user_id', Auth::id())
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        Order::cancelExpiredOrders();
        Product::releaseExpiredLocks();

        $order->refresh();
        $order->load('items.product.images', 'paymentLogs', 'reviews');
        return view('orders.show', compact('order'));
    }

    public function markAsDelivered(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        if ($order->status !== 'shipped') {
            return redirect()->back()->with('error', 'Pesanan belum dalam status dikirim.');
        }

        $order->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);

        app(PaymentService::class)->settleCommission($order);

        return redirect()->back()->with('success', 'Pesanan selesai! Terima kasih telah mengonfirmasi penerimaan barang.');
    }

    public function cancel(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        if ($order->status !== 'pending_payment') {
            return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah diproses.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            $freshOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            if (!$freshOrder || $freshOrder->status !== 'pending_payment') {
                return;
            }

            $freshOrder->load('items.product');
            foreach ($freshOrder->items as $item) {
                if ($item->product_id) {
                    $product = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($product && $product->status === 'locked') {
                        $product->unlock();
                    }
                }
            }

            $freshOrder->update(['status' => 'cancelled']);
        });

        return redirect()->route('orders.index')
            ->with('success', 'Pesanan berhasil dibatalkan dan produk telah dikembalikan ke katalog.');
    }

    public function storeReview(Request $request, Order $order)
    {
        // Pastikan order milik pembeli yang login & sudah selesai (delivered)
        abort_if($order->user_id !== Auth::id() || $order->status !== 'delivered', 403);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|min:10',
            'photos.*'   => 'nullable|image|max:2048',
        ]);

        // Pastikan produk memang ada di dalam pesanan ini (mencegah manipulasi product_id)
        $productInOrder = $order->items()->where('product_id', $request->product_id)->exists();
        if (!$productInOrder) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan pada pesanan ini.');
        }

        // Cek duplikasi ulasan
        $exists = Review::where('user_id', Auth::id())
            ->where('order_id', $order->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Kamu sudah memberikan ulasan untuk produk ini.');
        }

        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photos[] = $photo->store('reviews', 'public');
            }
        }

        Review::create([
            'user_id'     => Auth::id(),
            'product_id'  => $request->product_id,
            'order_id'    => $order->id,
            'rating'      => $request->rating,
            'comment'     => $request->comment,
            'photos'      => $photos,
            'is_approved' => true,  // Auto-approved: ulasan dari pembeli terverifikasi
        ]);

        return redirect()->back()->with('success', 'Ulasan berhasil dikirim! Terima kasih.');
    }
}