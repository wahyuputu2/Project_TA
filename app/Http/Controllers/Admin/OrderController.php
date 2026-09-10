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

    public function show(Order $order)
    {
        $order->load('user', 'items.product.images', 'paymentLogs', 'store');
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status'          => 'required|string',
            'tracking_number' => 'nullable|string|max:100',
            'notes'           => 'nullable|string',
        ]);

        $updates = ['status' => $request->status];

        if ($request->filled('tracking_number')) {
            $updates['tracking_number'] = $request->tracking_number;
        }
        if ($request->has('notes')) {
            $updates['notes'] = $request->notes;
        }
        if ($request->status === 'shipped' && !$order->shipped_at) {
            $updates['shipped_at'] = now();
        }
        if ($request->status === 'delivered' && !$order->delivered_at) {
            $updates['delivered_at'] = now();
        }
        if ($request->status === 'paid' && !$order->paid_at) {
            $updates['paid_at'] = now();

            // Buat komisi jika belum ada (kasus manual/transfer)
            $this->createCommissionIfNotExists($order);
        }
        if ($request->status === 'cancelled') {
            $order->load('items.product');
            $order->items->each(fn($item) => $item->product?->unlock());

            // Refund komisi jika ada
            $this->refundCommission($order);
        }
        if ($request->status === 'delivered') {
            $order->load('items.product');
            $order->items->each(fn($item) => $item->product?->markSold());

            // ✅ Settle komisi → saldo seller bertambah
            $this->settleCommission($order);
        }

        $order->update($updates);

        return redirect()->back()->with('success', 'Pesanan berhasil diperbarui!');
    }

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

    // ══════════════════════════════════════
    // PRIVATE METHODS
    // ══════════════════════════════════════

    /**
     * Buat komisi otomatis saat order paid
     * (dipanggil jika komisi belum ada)
     */
    private function createCommissionIfNotExists(Order $order): void
    {
        if (Commission::where('order_id', $order->id)->exists()) return;

        $store = $order->store;
        if (!$store) {
            Log::warning("Order {$order->order_number} tidak punya store_id");
            return;
        }

        $rate             = $store->commission_rate ?? 10;
        $orderAmount      = $order->subtotal;
        $commissionAmount = round($orderAmount * ($rate / 100), 2);
        $sellerAmount     = $orderAmount - $commissionAmount;

        Commission::create([
            'order_id'         => $order->id,
            'store_id'         => $store->id,
            'order_amount'     => $orderAmount,
            'commission_rate'  => $rate,
            'commission_amount'=> $commissionAmount,
            'seller_amount'    => $sellerAmount,
            'status'           => 'pending',
        ]);

        Log::info("Komisi dibuat manual: Order {$order->order_number}, Store {$store->name}");
    }

    /**
     * Settle komisi → saldo seller bertambah
     * Dipanggil saat status berubah ke "delivered"
     */
    private function settleCommission(Order $order): void
    {
        $commission = Commission::where('order_id', $order->id)
                        ->where('status', 'pending')
                        ->first();

        if (!$commission) {
            // Coba buat dulu jika belum ada
            $this->createCommissionIfNotExists($order);
            $commission = Commission::where('order_id', $order->id)
                            ->where('status', 'pending')
                            ->first();
        }

        if (!$commission) {
            Log::warning("Tidak ada komisi pending untuk order {$order->order_number}");
            return;
        }

        $commission->update([
            'status'     => 'settled',
            'settled_at' => now(),
        ]);

        // ✅ Tambah saldo seller
        $commission->store->increment('balance', $commission->seller_amount);
        $commission->store->increment('total_sales');

        Log::info("Komisi settled: Order {$order->order_number}, Store {$commission->store->name}, +Rp {$commission->seller_amount}");
    }

    /**
     * Refund komisi jika order dibatalkan
     * Dipanggil saat status berubah ke "cancelled"
     */
    private function refundCommission(Order $order): void
    {
        $commission = Commission::where('order_id', $order->id)
                        ->whereIn('status', ['pending', 'settled'])
                        ->first();

        if (!$commission) return;

        // Jika sudah settled, kurangi saldo seller kembali
        if ($commission->status === 'settled') {
            $store = $commission->store;
            if ($store && $store->balance >= $commission->seller_amount) {
                $store->decrement('balance', $commission->seller_amount);
                $store->decrement('total_sales');
            }
        }

        $commission->update(['status' => 'refunded']);

        Log::info("Komisi refunded: Order {$order->order_number}");
    }
}