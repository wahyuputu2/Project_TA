<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Commission;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function webhook(Request $request)
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        try {
            $notification      = new Notification();
            $orderId           = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $transactionId     = $notification->transaction_id;
            $paymentType       = $notification->payment_type;
            $fraudStatus       = $notification->fraud_status;
            $statusCode        = $notification->status_code;
            $signatureKey      = $notification->signature_key;

            // Verifikasi signature key jika server_key terisi
            $serverKey = config('services.midtrans.server_key');
            if (!empty($serverKey) && !empty($signatureKey)) {
                $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
                if ($signatureKey !== $expectedSignature) {
                    Log::warning("Webhook Error: Signature key mismatch for order {$orderId}");
                    return response()->json(['status' => 'invalid_signature'], 403);
                }
            }

            Log::info("Webhook: order={$orderId} status={$transactionStatus}");

            $order = Order::where('order_number', $orderId)->first();
            if (!$order) {
                return response()->json(['status' => 'order_not_found'], 404);
            }

            // Simpan log
            PaymentLog::create([
                'order_id'           => $order->id,
                'transaction_id'     => $transactionId,
                'payment_type'       => $paymentType,
                'transaction_status' => $transactionStatus,
                'gross_amount'       => $grossAmount,
                'raw_response'       => $request->all(),
            ]);

            if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
                $this->markAsPaid($order, $paymentType);
            } elseif ($transactionStatus === 'settlement') {
                $this->markAsPaid($order, $paymentType);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $this->markAsCancelled($order);
            } elseif ($transactionStatus === 'refund') {
                $order->update(['status' => 'refunded']);
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error("Webhook Error: " . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    private function markAsPaid(Order $order, string $paymentType): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $paymentType) {
            $freshOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            if (!$freshOrder || $freshOrder->status !== 'pending_payment') {
                return;
            }

            $freshOrder->update([
                'status'         => 'paid',
                'paid_at'        => now(),
                'payment_method' => $paymentType,
            ]);

            // ✅ Langsung mark produk sebagai SOLD saat bayar berhasil
            $freshOrder->load('items.product');
            $freshOrder->items->each(function ($item) {
                if ($item->product_id) {
                    $product = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($product) {
                        $product->markSold();
                        Log::info("Produk [{$product->name}] ditandai SOLD setelah pembayaran");
                    }
                }
            });

            // ✅ Buat komisi otomatis
            $this->createCommission($freshOrder);

            Log::info("Order {$freshOrder->order_number} dibayar via {$paymentType}");
        });
    }

    private function markAsCancelled(Order $order): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            $freshOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            if (!$freshOrder || in_array($freshOrder->status, ['paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])) {
                return;
            }

            $freshOrder->load('items.product');
            $freshOrder->items->each(function ($item) {
                if ($item->product_id) {
                    $product = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($product && $product->status === 'locked') {
                        $product->unlock();
                    }
                }
            });

            $freshOrder->update(['status' => 'cancelled']);
            Log::info("Order {$freshOrder->order_number} dibatalkan");
        });
    }

    private function createCommission(Order $order): void
    {
        // Jika sudah ada komisi, skip
        if (Commission::where('order_id', $order->id)->exists()) return;

        $store = $order->store;
        if (!$store) {
            Log::warning("Order {$order->order_number} tidak punya store_id, komisi tidak dibuat");
            return;
        }

        $commissionRate   = $store->commission_rate ?? 10;
        $orderAmount      = $order->subtotal; // tanpa ongkir
        $commissionAmount = round($orderAmount * ($commissionRate / 100), 2);
        $sellerAmount     = $orderAmount - $commissionAmount;

        Commission::create([
            'order_id'         => $order->id,
            'store_id'         => $store->id,
            'order_amount'     => $orderAmount,
            'commission_rate'  => $commissionRate,
            'commission_amount'=> $commissionAmount,
            'seller_amount'    => $sellerAmount,
            'status'           => 'pending',
        ]);

        Log::info("Komisi dibuat: Order {$order->order_number}, Store {$store->name}, Seller dapat Rp {$sellerAmount}");
    }

    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $status  = $request->transaction_status;
        $order   = Order::where('order_number', $orderId)
                        ->where('user_id', auth()->id())
                        ->first();

        if (!$order) return redirect()->route('orders.index');

        // Jangan sembarang percaya query parameter browser; verifikasi via status order atau Midtrans API
        if ($order->status === 'paid') {
            return redirect()->route('checkout.success', $order)
                ->with('success', 'Pembayaran berhasil dikonfirmasi!');
        }

        // Cek langsung status transaksi ke Midtrans jika belum 'paid'
        try {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            if (!empty(Config::$serverKey)) {
                $midtransStatus = \Midtrans\Transaction::status($order->order_number);
                if (in_array($midtransStatus->transaction_status, ['settlement', 'capture'])) {
                    $this->markAsPaid($order, $midtransStatus->payment_type ?? 'midtrans');
                }
            }
        } catch (\Exception $e) {
            Log::info("Finish verify check: " . $e->getMessage());
        }

        return redirect()->route('checkout.success', $order)
            ->with('success', 'Pembayaran diproses!');
    }

    public function unfinish(Request $request)
    {
        $order = Order::where('order_number', $request->order_id)->first();
        return redirect()->route('orders.show', $order)
            ->with('info', 'Pembayaran belum diselesaikan.');
    }

    public function error(Request $request)
    {
        $order = Order::where('order_number', $request->order_id)->first();
        return redirect()->route('orders.show', $order)
            ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }
}