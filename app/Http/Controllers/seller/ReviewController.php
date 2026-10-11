<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Tampilkan semua ulasan dari produk milik toko seller yang sedang login.
     * Seller hanya bisa melihat ulasan produknya sendiri.
     */
    public function index(Request $request)
    {
        $store = Auth::user()->store;

        if (!$store) {
            return redirect()->route('home')->with('error', 'Toko tidak ditemukan.');
        }

        // Ambil semua product_id milik toko ini
        $productIds = $store->products()->pluck('id');

        // Ambil review yang hanya berkaitan dengan produk toko ini
        $reviews = Review::with(['user', 'product.images', 'order'])
            ->whereIn('product_id', $productIds)
            ->when($request->rating, fn($q) => $q->where('rating', $request->rating))
            ->latest()
            ->paginate(15);

        // Statistik
        $totalReviews   = Review::whereIn('product_id', $productIds)->count();
        $avgRating      = Review::whereIn('product_id', $productIds)->avg('rating') ?? 0;
        $rating5        = Review::whereIn('product_id', $productIds)->where('rating', 5)->count();
        $rating4        = Review::whereIn('product_id', $productIds)->where('rating', 4)->count();
        $rating3        = Review::whereIn('product_id', $productIds)->where('rating', 3)->count();
        $rating2        = Review::whereIn('product_id', $productIds)->where('rating', 2)->count();
        $rating1        = Review::whereIn('product_id', $productIds)->where('rating', 1)->count();

        return view('seller.reviews.index', compact(
            'reviews', 'store',
            'totalReviews', 'avgRating',
            'rating5', 'rating4', 'rating3', 'rating2', 'rating1'
        ));
    }
    public function toggle(Review $review)
    {
        // Pastikan review ini milik produk toko seller yang login
        $store = Auth::user()->store;
        if (!$store) {
            abort(403);
        }

        $productIds = $store->products()->pluck('id');
        if (!$productIds->contains($review->product_id)) {
            abort(403, 'Anda tidak berhak mengubah ulasan ini.');
        }

        $review->update(['is_approved' => !$review->is_approved]);

        $status = $review->is_approved ? 'ditampilkan' : 'disembunyikan';
        return redirect()->back()->with('success', "Ulasan berhasil {$status}.");
    }
}
