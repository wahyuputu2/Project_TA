<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar seluruh produk dari semua toko untuk pemantauan Admin.
     * Mendukung pencarian nama, filter status (available, locked, sold), dan filter toko.
     */
    public function index(Request $request)
    {
        $products = Product::with(['category', 'brand', 'images', 'store'])
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->store_id, fn($q) => $q->where('store_id', $request->store_id))
            ->latest()
            ->paginate(20);

        $stores = Store::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('admin.products.index', compact('products', 'stores'));
    }

    /**
     * Tampilkan detail produk secara lengkap untuk pemantauan Admin.
     * Termasuk foto, informasi produk, stok/status, kondisi, dan informasi seller.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'store.user', 'reviews.user']);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Pengelolaan produk biasa (tambah, edit, hapus) bukan wewenang Admin,
     * melainkan kewenangan masing-masing Seller.
     */
    public function create()
    {
        abort(403, 'Admin tidak memiliki hak akses untuk menambah produk.');
    }

    public function store()
    {
        abort(403, 'Admin tidak memiliki hak akses untuk menambah produk.');
    }

    public function edit()
    {
        abort(403, 'Admin tidak memiliki hak akses untuk mengedit produk seller.');
    }

    public function update()
    {
        abort(403, 'Admin tidak memiliki hak akses untuk memperbarui produk seller.');
    }

    public function destroy()
    {
        abort(403, 'Admin tidak memiliki hak akses untuk menghapus produk seller.');
    }
}