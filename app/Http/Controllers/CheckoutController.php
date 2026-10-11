<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\PaymentService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService    $cart,
        private PaymentService $payment,
        private ShippingService $shipping,
    ) {}

    public function index()
    {
        Product::releaseExpiredLocks();

        $items = $this->cart->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kamu kosong atau masa simpan produk telah berakhir.');
        }

        // Validasi ketersediaan setiap produk di keranjang
        foreach ($items as $item) {
            $p = $item->product;
            if (!$p || $p->isSold() || ($p->isLocked() && !$this->cart->isLockedByCurrentUser($p))) {
                $this->cart->remove($p);
                return redirect()->route('cart.index')
                    ->with('error', 'Produk "' . ($p?->name ?? 'item') . '" sudah tidak tersedia atau sedang dipesan oleh pembeli lain.');
            }
        }

        $provinces = $this->shipping->getProvinces();
        $user      = Auth::user();
        return view('checkout.index', compact('items', 'provinces', 'user'));
    }

    public function getCities(Request $request)
    {
        $cities = $this->shipping->getCities($request->province_id);
        return response()->json($cities);
    }

    public function calculateShipping(Request $request)
    {
        $items  = $this->cart->getItems();
        $weight = $items->sum(fn($c) => $c->product->weight_gram ?? 500);
        $results = $this->shipping->calculate(
            config('services.rajaongkir.origin_city_id', 17),
            $request->city_id,
            max(1, $weight),
            $request->courier
        );
        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_name'        => 'required|string|max:100',
            'shipping_phone'       => 'required|string|max:20',
            'shipping_address'     => 'required|string',
            'shipping_city'        => 'required|string',
            'shipping_city_id'     => 'required',
            'shipping_province'    => 'required|string',
            'shipping_postal_code' => 'required|string|max:10',
            'courier'              => 'required|string',
            'courier_service'      => 'required|string',
            'shipping_cost'        => 'required|numeric|min:0',
        ]);

        Product::releaseExpiredLocks();

        $items = $this->cart->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kamu kosong atau masa simpan produk telah habis.');
        }

        $subtotal = $this->cart->total();
        $total    = $subtotal + $request->shipping_cost;
        $order    = null;

        try {
            DB::transaction(function () use ($request, $items, $subtotal, $total, &$order) {
                // 1. Verifikasi dan lock produk secara pesimistik (lockForUpdate)
                $lockedProducts = [];
                foreach ($items as $cartItem) {
                    $p = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();
                    if (!$p || $p->isSold()) {
                        throw new \Exception("Produk '{$cartItem->product->name}' sudah terjual.");
                    }
                    if ($p->isLocked() && !$this->cart->isLockedByCurrentUser($p)) {
                        throw new \Exception("Produk '{$cartItem->product->name}' sedang dipesan oleh pembeli lain.");
                    }
                    $lockedProducts[$cartItem->id] = $p;
                }

                $firstProduct = $items->first()?->product;
                $storeId      = $firstProduct?->store_id ?? null;

                $order = Order::create([
                    'user_id'              => Auth::id(),
                    'store_id'             => $storeId,
                    'subtotal'             => $subtotal,
                    'shipping_cost'        => $request->shipping_cost,
                    'total'                => $total,
                    'status'               => 'pending_payment',
                    'expires_at'           => now()->addMinutes(15), // Durasi penguncian stok 15 menit
                    'shipping_name'        => $request->shipping_name,
                    'shipping_phone'       => $request->shipping_phone,
                    'shipping_address'     => $request->shipping_address,
                    'shipping_city'        => $request->shipping_city,
                    'shipping_province'    => $request->shipping_province,
                    'shipping_postal_code' => $request->shipping_postal_code,
                    'courier'              => strtoupper($request->courier),
                    'courier_service'      => $request->courier_service,
                ]);

                foreach ($items as $cartItem) {
                    $p = $lockedProducts[$cartItem->id] ?? $cartItem->product;
                    $order->items()->create([
                        'product_id'        => $p->id,
                        'product_name'      => $p->name,
                        'product_size'      => $p->size,
                        'product_condition' => $p->conditionLabel(),
                        'price'             => $p->price,
                    ]);

                    // ✅ Kunci produk dengan identifier pesanan
                    $p->update([
                        'status'            => 'locked',
                        'locked_by_session' => 'order_' . $order->order_number,
                        'locked_at'         => now(),
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        // Coba buat Midtrans token
        $snap = null;
        try {
            $snap = $this->payment->createTransaction($order);
        } catch (\Exception $e) {
            Log::warning('Midtrans error: ' . $e->getMessage());
            $order->update(['payment_method' => 'manual_transfer']);
        }

        // Hapus isi keranjang TANPA melepas lock produk (karena sekarang produk dikunci oleh Order)
        $this->cart->clear(unlock: false);

        if ($snap && isset($snap['token'])) {
            return view('checkout.payment', compact('order', 'snap'));
        }

        return redirect()->route('checkout.success', $order)
            ->with('info', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran dalam waktu 15 menit.');
    }

    public function success(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);
        return view('checkout.success', compact('order'));
    }
}