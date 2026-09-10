<?php
namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $store = Auth::user()->store;

        $totalProducts   = $store->products()->count();
        $availableProducts = $store->products()->where('status','available')->count();
        $soldProducts    = $store->products()->where('status','sold')->count();
        $totalOrders     = $store->orders()->count();
        $pendingOrders   = $store->orders()->where('status','paid')->count();
        $totalRevenue    = $store->totalRevenue();
        $pendingBalance  = $store->pendingBalance();
        $currentBalance  = $store->balance;

        $recentOrders = $store->orders()
            ->with('user','items')
            ->latest()->limit(5)->get();

        $topProducts = $store->products()
            ->withCount('orderItems as sold_count')
            ->orderByDesc('sold_count')
            ->limit(5)->get();

        return view('seller.dashboard', compact(
            'store','totalProducts','availableProducts','soldProducts',
            'totalOrders','pendingOrders','totalRevenue','pendingBalance',
            'currentBalance','recentOrders','topProducts'
        ));
    }
}