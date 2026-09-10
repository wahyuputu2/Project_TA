<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    private string $sessionId;

    public function __construct() {
        $this->sessionId = session()->getId();
    }

    public function add(Product $product): array
    {
        Product::releaseExpiredLocks();

        if (!$product->isAvailable()) {
            return ['success' => false, 'message' => 'Maaf, produk ini sudah tidak tersedia.'];
        }

        // Lock produk
        $product->lockForSession($this->sessionId);

        Cart::updateOrCreate(
            ['product_id' => $product->id],
            [
                'user_id'    => Auth::id(),
                'session_id' => $this->sessionId,
                'expires_at' => now()->addMinutes(15),
            ]
        );

        return ['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang! (Tersimpan 15 menit)'];
    }

    public function remove(Product $product): void
    {
        Cart::where('product_id', $product->id)->delete();
        if ($product->locked_by_session === $this->sessionId) {
            $product->unlock();
        }
    }

    public function getItems()
    {
        $query = Cart::with('product.images','product.brand','product.category');
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $this->sessionId);
        }
        return $query->get()->filter(fn($c) => !$c->isExpired());
    }

    public function clear(): void
    {
        $this->getItems()->each(fn($c) => $this->remove($c->product));
    }

    public function total(): float
    {
        return $this->getItems()->sum(fn($c) => $c->product->price);
    }

    public function count(): int
    {
        return $this->getItems()->count();
    }
}