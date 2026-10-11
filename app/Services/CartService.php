<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getSessionId(): string
    {
        return session()->getId() ?: 'guest_' . md5(request()->ip());
    }

    public function getLockKey(): string
    {
        return Auth::check() ? 'user_' . Auth::id() : $this->getSessionId();
    }

    public function isLockedByCurrentUser(Product $product): bool
    {
        if (!$product->isLocked()) {
            return false;
        }

        $lockKey = $this->getLockKey();
        if ($product->locked_by_session === $lockKey) {
            return true;
        }

        $sid = $this->getSessionId();
        if ($product->locked_by_session === $sid) {
            return true;
        }

        if (Auth::check() && $product->locked_by_session === 'user_' . Auth::id()) {
            return true;
        }

        return false;
    }

    public function add(Product $product): array
    {
        Product::releaseExpiredLocks();

        return DB::transaction(function () use ($product) {
            $lockedProduct = Product::where('id', $product->id)->lockForUpdate()->first();

            if (!$lockedProduct) {
                return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
            }

            if ($lockedProduct->isSold()) {
                return ['success' => false, 'message' => 'Maaf, produk ini sudah terjual.'];
            }

            $lockKey = $this->getLockKey();
            $sessionId = $this->getSessionId();

            if ($lockedProduct->isLocked()) {
                // Jika sudah dikunci oleh sesi / user yang sama di keranjang, perpanjang waktu simpan
                if ($this->isLockedByCurrentUser($lockedProduct)) {
                    Cart::updateOrCreate(
                        ['product_id' => $lockedProduct->id],
                        [
                            'user_id'    => Auth::id(),
                            'session_id' => $sessionId,
                            'expires_at' => now()->addMinutes(15),
                        ]
                    );
                    return ['success' => true, 'message' => 'Produk sudah ada di keranjang kamu.'];
                }

                return ['success' => false, 'message' => 'Maaf, produk ini sedang dipesan oleh pembeli lain.'];
            }

            // Lock produk untuk sesi / user ini
            $lockedProduct->lockForSession($lockKey);

            Cart::updateOrCreate(
                ['product_id' => $lockedProduct->id],
                [
                    'user_id'    => Auth::id(),
                    'session_id' => $sessionId,
                    'expires_at' => now()->addMinutes(15),
                ]
            );

            return ['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang! (Tersimpan 15 menit)'];
        });
    }

    public function remove(Product $product): void
    {
        $query = Cart::where('product_id', $product->id);
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $this->getSessionId());
        }
        $query->delete();

        if ($this->isLockedByCurrentUser($product)) {
            $product->unlock();
        }
    }

    public function getItems()
    {
        Product::releaseExpiredLocks();

        $query = Cart::with('product.images','product.brand','product.category');
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $this->getSessionId());
        }

        $items = $query->get();

        // Validasi dan bersihkan item kadaluwarsa atau yang stoknya tidak valid lagi
        return $items->filter(function ($item) {
            $product = $item->product;

            if (!$product) {
                $item->delete();
                return false;
            }

            if ($item->isExpired()) {
                if ($this->isLockedByCurrentUser($product)) {
                    $product->unlock();
                }
                $item->delete();
                return false;
            }

            if ($product->isSold()) {
                $item->delete();
                return false;
            }

            // Jika produk terkunci oleh sesi lain (misal pesanan pembeli lain)
            if ($product->isLocked() && !$this->isLockedByCurrentUser($product)) {
                $item->delete();
                return false;
            }

            return true;
        });
    }

    public function clear(bool $unlock = true): void
    {
        if ($unlock) {
            $this->getItems()->each(fn($c) => $this->remove($c->product));
        } else {
            $query = Cart::query();
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $this->getSessionId());
            }
            $query->delete();
        }
    }

    public function migrateGuestCart(int $userId, ?string $oldSessionId): void
    {
        if (!$oldSessionId) return;

        Cart::where('session_id', $oldSessionId)
            ->whereNull('user_id')
            ->update([
                'user_id' => $userId,
            ]);

        Product::where('status', 'locked')
            ->where('locked_by_session', $oldSessionId)
            ->update([
                'locked_by_session' => 'user_' . $userId,
            ]);
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