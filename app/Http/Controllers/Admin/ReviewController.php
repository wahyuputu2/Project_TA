<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['user', 'product', 'order'])
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'approved') {
                    $q->where('is_approved', true);
                } elseif ($request->status === 'pending') {
                    $q->where('is_approved', false);
                }
            })
            ->when($request->rating, fn($q) => $q->where('rating', $request->rating))
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, Review $review)
    {
        $review->update(['is_approved' => !$review->is_approved]);

        $status = $review->is_approved ? 'disetujui' : 'disembunyikan';

        return redirect()->back()->with('success', "Ulasan berhasil {$status}.");
    }

    public function destroy(Review $review)
    {
        if ($review->photos) {
            foreach ($review->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $review->delete();

        return redirect()->back()->with('success', 'Ulasan berhasil dihapus.');
    }
}