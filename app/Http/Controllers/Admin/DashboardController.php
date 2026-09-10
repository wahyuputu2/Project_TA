<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Store;
use App\Models\Review;
use App\Models\Withdrawal;

class DashboardController extends Controller
{
    public function index()
    {
        // Produk
        $totalProducts     = Product::count();
        $availableProducts = Product::where('status', 'available')->count();
        $soldProducts      = Product::where('status', 'sold')->count();

        // Pesanan
        $totalOrders   = Order::count();
        $pendingOrders = Order::where('status', 'paid')->count();
        $needProcess   = Order::whereIn('status', ['paid', 'processing'])->count();

        // Pendapatan
        $totalRevenue  = Order::where('status', 'delivered')->sum('total');

        // User
        $totalUsers     = User::count();
        $totalSellers   = User::where('role', 'seller')->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Toko
        $totalStores   = Store::count();
        $pendingStores = Store::where('status', 'pending')->count();

        // Ulasan
        $totalReviews   = Review::count();
        $pendingReviews = Review::where('is_approved', false)->count();

        // Withdraw
        $pendingWithdraw      = Withdrawal::where('status', 'pending')->sum('amount');
        $pendingWithdrawCount = Withdrawal::where('status', 'pending')->count();

        // Recent data
        $recentOrders = Order::with('user', 'store')
            ->latest()->limit(8)->get();

        $recentStores = Store::with('user')
            ->latest()->limit(5)->get();

        $topProducts = Product::withCount(['orderItems as sold_count'])
            ->having('sold_count', '>', 0)
            ->with('images')
            ->orderByDesc('sold_count')
            ->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalProducts', 'availableProducts', 'soldProducts',
            'totalOrders', 'pendingOrders', 'needProcess',
            'totalRevenue',
            'totalUsers', 'totalSellers', 'totalCustomers',
            'totalStores', 'pendingStores',
            'totalReviews', 'pendingReviews',
            'pendingWithdraw', 'pendingWithdrawCount',
            'recentOrders', 'recentStores', 'topProducts'
        ));
    }
}