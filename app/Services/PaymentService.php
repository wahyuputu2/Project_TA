<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentLog;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentService
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    public function createTransaction(Order $order): array
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
                'phone'      => $order->shipping_phone,
                'billing_address' => [
                    'address'     => $order->shipping_address,
                    'city'        => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code'=> 'IDN',
                ],
                'shipping_address' => [
                    'first_name'  => $order->shipping_name,
                    'phone'       => $order->shipping_phone,
                    'address'     => $order->shipping_address,
                    'city'        => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code'=> 'IDN',
                ],
            ],
            'item_details' => $this->buildItemDetails($order),

            // URL redirect setelah pembayaran
            'callbacks' => [
                'finish' => route('payment.finish'),
            ],
        ];

        $snap = Snap::createTransaction($params);

        $order->update([
            'payment_token' => $snap->token,
            'payment_url'   => $snap->redirect_url,
        ]);

        return [
            'token'        => $snap->token,
            'redirect_url' => $snap->redirect_url,
        ];
    }

    private function buildItemDetails(Order $order): array
    {
        $items = $order->items->map(fn($item) => [
            'id'       => (string) $item->product_id,
            'price'    => (int) $item->price,
            'quantity' => 1,
            'name'     => substr($item->product_name, 0, 50),
        ])->toArray();

        // Tambahkan ongkir sebagai item jika ada
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id'       => 'ONGKIR',
                'price'    => (int) $order->shipping_cost,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim (' . $order->courier . ' ' . $order->courier_service . ')',
            ];
        }

        return $items;
    }

    private function markPaid(Order $order): void
{
    $order->update(['status' => 'paid', 'paid_at' => now()]);
    $order->items->each(fn($i) => $i->product?->markSold());

    // Hitung & simpan komisi otomatis
    $this->calculateCommission($order);
}

private function calculateCommission(Order $order): void
{
    $store = $order->store;
    if (!$store) return;

    $orderAmount     = $order->subtotal; // tanpa ongkir
    $commissionRate  = $store->commission_rate;
    $commissionAmount = $orderAmount * ($commissionRate / 100);
    $sellerAmount    = $orderAmount - $commissionAmount;

    Commission::create([
        'order_id'         => $order->id,
        'store_id'         => $store->id,
        'order_amount'     => $orderAmount,
        'commission_rate'  => $commissionRate,
        'commission_amount'=> $commissionAmount,
        'seller_amount'    => $sellerAmount,
        'status'           => 'pending',
    ]);
}

    // Dipanggil saat order delivered
    public function settleCommission(Order $order): void
    {
         $commission = Commission::where('order_id', $order->id)->first();
        if (!$commission || $commission->status === 'settled') return;

        $commission->update([
            'status'      => 'settled',
            'settled_at'  => now(),
        ]);

    // Tambah saldo seller
    $commission->store->addBalance($commission->seller_amount);
    $commission->store->increment('total_sales');
    }

}