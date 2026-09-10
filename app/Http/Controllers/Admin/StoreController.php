<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::with('user')
            ->withCount('products')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('name','like','%'.$request->search.'%'))
            ->latest()
            ->paginate(20);

        // Stats
        $totalStores     = Store::count();
        $pendingStores   = Store::where('status','pending')->count();
        $activeStores    = Store::where('status','active')->count();
        $suspendedStores = Store::where('status','suspended')->count();

        return view('admin.stores.index', compact(
            'stores','totalStores','pendingStores','activeStores','suspendedStores'
        ));
    }

    public function verify(Store $store)
    {
        $store->update(['status' => 'active']);

        // Pastikan user pemilik toko dapat role seller
        $store->user->update(['role' => 'seller']);

        return redirect()->back()
            ->with('success', "Toko {$store->name} berhasil diverifikasi & diaktifkan!");
    }

    public function suspend(Store $store)
    {
        $store->update(['status' => 'suspended']);

        return redirect()->back()
            ->with('success', "Toko {$store->name} telah ditangguhkan.");
    }
}