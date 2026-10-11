<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockLockTest extends TestCase
{
    use RefreshDatabase;

    private User $buyerA;
    private User $buyerB;
    private Store $store;
    private Category $category;
    private Brand $brand;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $seller = User::factory()->create(['role' => 'seller']);
        $this->store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Thrift Store Official',
            'slug' => 'thrift-store-official',
            'status' => 'active',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
        ]);

        $this->category = Category::create([
            'name' => 'Jaket',
            'slug' => 'jaket',
        ]);

        $this->brand = Brand::create([
            'name' => 'Nike',
            'slug' => 'nike',
        ]);

        $this->buyerA = User::factory()->create(['role' => 'customer']);
        $this->buyerB = User::factory()->create(['role' => 'customer']);

        $this->product = Product::create([
            'store_id' => $this->store->id,
            'name' => 'Vintage Nike Windbreaker',
            'slug' => 'vintage-nike-windbreaker',
            'description' => 'Original vintage windbreaker jacket in mint condition.',
            'price' => 250000,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'size' => 'L',
            'condition' => 'like_new',
            'weight_gram' => 400,
            'status' => 'available',
        ]);
    }

    /**
     * Skenario 1 & 3: Pembeli A checkout -> pesanan dibuat dengan hitung mundur 15 menit
     * Refresh halaman Pesanan -> hitung mundur tetap akurat sesuai waktu di database.
     */
    public function test_buyer_a_checkout_creates_pending_order_with_countdown_and_persists_on_refresh(): void
    {
        // Buyer A menambahkan ke keranjang via HTTP agar session id tersinkronisasi
        $this->actingAs($this->buyerA);
        $addResponse = $this->post(route('cart.add', $this->product));
        $addResponse->assertSessionHas('success');

        // Buyer A submit checkout
        $response = $this->post(route('checkout.store'), [
            'shipping_name' => 'Pembeli A',
            'shipping_phone' => '08123456789',
            'shipping_address' => 'Jl. Merdeka No. 10',
            'shipping_city' => 'Bandung',
            'shipping_city_id' => 17,
            'shipping_province' => 'Jawa Barat',
            'shipping_postal_code' => '40115',
            'courier' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 15000,
        ]);

        $this->product->refresh();
        $this->assertEquals('locked', $this->product->status);

        $order = Order::where('user_id', $this->buyerA->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending_payment', $order->status);
        $this->assertNotNull($order->expires_at);
        $this->assertTrue($order->expires_at->isFuture());

        // Buka halaman pesanan: periksa ada hitung mundur
        $ordersResponse = $this->get(route('orders.index'));
        $ordersResponse->assertStatus(200);
        $ordersResponse->assertSee('Sisa Waktu Pembayaran');
        $ordersResponse->assertSee($order->effective_expires_at->toIso8601String());

        // Skenario 3: Refresh halaman Pesanan
        $refreshResponse = $this->get(route('orders.show', $order));
        $refreshResponse->assertStatus(200);
        $refreshResponse->assertSee('Sisa Waktu Pembayaran');
        $refreshResponse->assertSee($order->effective_expires_at->toIso8601String());
    }

    /**
     * Skenario 2: Pembeli B membuka Beranda dan detail produk yang terkunci.
     * Produk menampilkan status "Sedang Dipesan" dan tidak dapat dibeli.
     */
    public function test_buyer_b_cannot_purchase_locked_product(): void
    {
        // Kunci produk seolah-olah dipesan pembeli A
        $this->product->update([
            'status' => 'locked',
            'locked_by_session' => 'order_TF-TEST1234',
            'locked_at' => now(),
        ]);

        $this->actingAs($this->buyerB);

        // Cek katalog / Beranda
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('SEDANG DIPESAN');

        // Cek halaman detail produk
        $detailResponse = $this->get(route('catalog.show', $this->product->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Sedang Dipesan (Stok Dikunci)');

        // Pembeli B mencoba memanipulasi request dengan menambahkan ke keranjang
        $addCartResponse = $this->post(route('cart.add', $this->product));
        $addCartResponse->assertSessionHas('error');

        $this->assertEquals(0, Cart::where('user_id', $this->buyerB->id)->count());
    }

    /**
     * Skenario 4: Pembayaran berhasil sebelum waktu habis.
     * Hitung mundur berhenti dan produk ditandai SOLD (stok tidak dilepaskan).
     */
    public function test_successful_payment_stops_countdown_and_marks_product_sold(): void
    {
        $this->actingAs($this->buyerA);
        $order = Order::create([
            'user_id' => $this->buyerA->id,
            'store_id' => $this->store->id,
            'subtotal' => 250000,
            'shipping_cost' => 15000,
            'total' => 265000,
            'status' => 'pending_payment',
            'expires_at' => now()->addMinutes(15),
            'shipping_name' => 'Pembeli A',
            'shipping_phone' => '08123456789',
            'shipping_address' => 'Jl. Merdeka No. 10',
            'shipping_city' => 'Bandung',
            'shipping_province' => 'Jawa Barat',
            'shipping_postal_code' => '40115',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_size' => $this->product->size,
            'product_condition' => $this->product->conditionLabel(),
            'price' => $this->product->price,
        ]);

        $this->product->update([
            'status' => 'locked',
            'locked_by_session' => 'order_' . $order->order_number,
            'locked_at' => now(),
        ]);

        // Simulasi pembayaran berhasil dari Midtrans finish/webhook
        $paymentController = app(\App\Http\Controllers\PaymentController::class);
        $method = new \ReflectionMethod($paymentController, 'markAsPaid');
        $method->setAccessible(true);
        $method->invoke($paymentController, $order, 'bank_transfer');

        $order->refresh();
        $this->product->refresh();

        $this->assertEquals('paid', $order->status);
        $this->assertEquals('sold', $this->product->status);

        // Halaman pesanan tidak boleh lagi menampilkan hitung mundur
        $ordersResponse = $this->get(route('orders.index'));
        $ordersResponse->assertDontSee('Sisa Waktu Pembayaran');
    }

    /**
     * Skenario 5: Jika waktu 15 menit habis dan pembayaran belum berhasil,
     * penguncian otomatis dilepaskan dan produk dapat dibeli kembali.
     */
    public function test_expired_order_releases_stock_automatically(): void
    {
        $this->actingAs($this->buyerA);
        $order = Order::create([
            'user_id' => $this->buyerA->id,
            'store_id' => $this->store->id,
            'subtotal' => 250000,
            'shipping_cost' => 15000,
            'total' => 265000,
            'status' => 'pending_payment',
            'expires_at' => now()->subMinute(), // Sudah lewat 15 menit
            'shipping_name' => 'Pembeli A',
            'shipping_phone' => '08123456789',
            'shipping_address' => 'Jl. Merdeka No. 10',
            'shipping_city' => 'Bandung',
            'shipping_province' => 'Jawa Barat',
            'shipping_postal_code' => '40115',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_size' => $this->product->size,
            'product_condition' => $this->product->conditionLabel(),
            'price' => $this->product->price,
        ]);

        $this->product->update([
            'status' => 'locked',
            'locked_by_session' => 'order_' . $order->order_number,
            'locked_at' => now()->subMinutes(16),
        ]);

        // Pembeli B membuka katalog atau sistem menjalankan pembersihan
        $this->actingAs($this->buyerB);
        Product::releaseExpiredLocks();

        $order->refresh();
        $this->product->refresh();

        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals('available', $this->product->status);

        // Pembeli B sekarang bisa menambahkan ke keranjang
        $cartResult = app(CartService::class)->add($this->product);
        $this->assertTrue($cartResult['success']);
    }

    /**
     * Skenario 6: Dua pembeli checkout hampir bersamaan.
     * Sistem hanya mengizinkan transaksi yang sesuai dengan stok tersedia.
     */
    public function test_concurrency_lock_allows_only_one_buyer(): void
    {
        // Pembeli A memasukkan ke keranjang
        $this->actingAs($this->buyerA);
        $resA = app(CartService::class)->add($this->product);
        $this->assertTrue($resA['success']);

        // Pembeli B mencoba menambahkan ke keranjang produk yang sama
        $this->actingAs($this->buyerB);
        $resB = app(CartService::class)->add($this->product);
        $this->assertFalse($resB['success']);
        $this->assertStringContainsString('dipesan oleh pembeli lain', $resB['message']);
    }

    /**
     * Skenario 7: Produk yang sudah dibayar, dikirim, atau selesai
     * tidak boleh kembali tersedia akibat proses kedaluwarsa.
     */
    public function test_paid_or_completed_orders_never_released_by_expiration(): void
    {
        $order = Order::create([
            'user_id' => $this->buyerA->id,
            'store_id' => $this->store->id,
            'subtotal' => 250000,
            'shipping_cost' => 15000,
            'total' => 265000,
            'status' => 'delivered',
            'expires_at' => now()->subDays(2),
            'shipping_name' => 'Pembeli A',
            'shipping_phone' => '08123456789',
            'shipping_address' => 'Jl. Merdeka No. 10',
            'shipping_city' => 'Bandung',
            'shipping_province' => 'Jawa Barat',
            'shipping_postal_code' => '40115',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_size' => $this->product->size,
            'product_condition' => $this->product->conditionLabel(),
            'price' => $this->product->price,
        ]);

        $this->product->update(['status' => 'sold']);

        // Jalankan pelepasan kedaluwarsa berulang-ulang (idempotensi)
        Order::cancelExpiredOrders();
        Product::releaseExpiredLocks();

        $this->product->refresh();
        $order->refresh();

        $this->assertEquals('sold', $this->product->status);
        $this->assertEquals('delivered', $order->status);
    }

    /**
     * Skenario 8: Keranjang lama tidak boleh digunakan untuk melewati penguncian stok.
     */
    public function test_stale_cart_cannot_bypass_lock(): void
    {
        // Pembeli B punya keranjang yang expires_at-nya sudah lewat
        Cart::create([
            'user_id' => $this->buyerB->id,
            'session_id' => 'session_b',
            'product_id' => $this->product->id,
            'expires_at' => now()->subMinutes(20),
        ]);

        // Pembeli A berhasil memesan produk
        $this->product->update([
            'status' => 'locked',
            'locked_by_session' => 'order_TF-XYZ',
            'locked_at' => now(),
        ]);

        $this->actingAs($this->buyerB);

        // Pembeli B mencoba membuka /checkout dengan keranjang lama
        $response = $this->get(route('checkout.index'));
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');

        // Pastikan keranjang lama otomatis dibersihkan
        $this->assertEquals(0, Cart::where('user_id', $this->buyerB->id)->count());
    }
}
