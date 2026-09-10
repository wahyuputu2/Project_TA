<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? '30';

        $salesData = Order::where('status','!=','cancelled')
            ->where('created_at','>=', now()->subDays($period))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as orders'))
            ->groupBy('date')->orderBy('date')->get();

        $totalRevenue   = Order::where('status','delivered')->sum('total');
        $totalOrders    = Order::where('status','!=','cancelled')->count();
        $pendingOrders  = Order::where('status','pending_payment')->count();

        $topProducts = Product::withCount(['orderItems as sold_count'])
            ->having('sold_count','>',0)
            ->orderByDesc('sold_count')->limit(10)->get();

        return view('admin.reports.index', compact(
            'salesData','totalRevenue','totalOrders','pendingOrders','topProducts','period'
        ));
    }
}