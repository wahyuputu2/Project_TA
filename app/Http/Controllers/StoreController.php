<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::where('status', 'active')
            ->withCount('products') // hapus filter having agar toko baru tetap muncul
            ->latest()
            ->paginate(12);

        return view('stores.index', compact('stores'));
    }

    public function show(Store $store)
    {
        abort_if(!$store->isActive(), 404);

        $products = $store->products()
            ->with(['images', 'brand', 'category'])
            ->available()
            ->latest()
            ->paginate(12);

        return view('stores.show', compact('store', 'products'));
    }
}