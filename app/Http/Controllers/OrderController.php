<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
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
        $order->load('items.product.images', 'paymentLogs', 'reviews');
        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        if ($order->status !== 'pending_payment') {
            return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah diproses.');
        }

        $order->load('items.product');
        $order->items->each(fn($item) => $item->product?->unlock());
        $order->update(['status' => 'cancelled']);

        return redirect()->route('orders.index')
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function storeReview(Request $request, Order $order)
    {
        abort_if($order->user_id !== Auth::id() || $order->status !== 'delivered', 403);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|min:10',
            'photos.*'   => 'nullable|image|max:2048',
        ]);

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
            'user_id'    => Auth::id(),
            'product_id' => $request->product_id,
            'order_id'   => $order->id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'photos'     => $photos,
        ]);

        return redirect()->back()->with('success', 'Ulasan berhasil dikirim! Terima kasih.');
    }
}