<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
        $items = $this->cart->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kosong.');
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

        $items = $this->cart->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $subtotal = $this->cart->total();
        $total    = $subtotal + $request->shipping_cost;
        $order    = null;

        DB::transaction(function () use ($request, $items, $subtotal, $total, &$order) {

            // ✅ Ambil store_id dari produk pertama di keranjang
            $firstProduct = $items->first()?->product;
            $storeId      = $firstProduct?->store_id ?? null;

            Log::info('Checkout - store_id dari produk:', [
                'product_id'   => $firstProduct?->id,
                'product_name' => $firstProduct?->name,
                'store_id'     => $storeId,
            ]);

            $order = Order::create([
                'user_id'              => Auth::id(),
                'store_id'             => $storeId, // ← ini yang penting
                'subtotal'             => $subtotal,
                'shipping_cost'        => $request->shipping_cost,
                'total'                => $total,
                'status'               => 'pending_payment',
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
                $p = $cartItem->product;
                $order->items()->create([
                    'product_id'        => $p->id,
                    'product_name'      => $p->name,
                    'product_size'      => $p->size,
                    'product_condition' => $p->conditionLabel(),
                    'price'             => $p->price,
                ]);
                // Lock produk
                $p->lockForSession(session()->getId());
            }
        });

        // Coba buat Midtrans token
        $snap = null;
        try {
            $snap = $this->payment->createTransaction($order);
        } catch (\Exception $e) {
            Log::warning('Midtrans error: ' . $e->getMessage());
            $order->update(['payment_method' => 'manual_transfer']);
        }

        // Clear cart
        $this->cart->clear();

        if ($snap && isset($snap['token'])) {
            return view('checkout.payment', compact('order', 'snap'));
        }

        return redirect()->route('checkout.success', $order)
            ->with('info', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }

    public function success(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);
        return view('checkout.success', compact('order'));
    }
}