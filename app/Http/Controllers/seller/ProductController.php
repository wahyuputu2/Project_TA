<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Ambil store milik seller yang login
     * Query langsung ke DB, bukan pakai relasi hasOne
     */
    private function sellerStore(): ?Store
    {
        $userId = Auth::id();

        $store = Store::where('user_id', $userId)
                      ->where('status', 'active')
                      ->first();

        // Debug log
        Log::info("SellerStore check - UserID: {$userId}, Store: " . ($store?->name ?? 'NULL') . ", StoreID: " . ($store?->id ?? 'NULL'));

        return $store;
    }

    public function index(Request $request)
    {
        $store = $this->sellerStore();

        if (!$store) {
            return redirect()->route('home')
                ->with('error', 'Toko tidak ditemukan atau belum aktif.');
        }

        $products = Product::where('store_id', $store->id)
            ->with(['category', 'brand', 'images'])
            ->when($request->search, fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('seller.products.index', compact('products', 'store'));
    }

    public function create()
    {
        $store = $this->sellerStore();

        if (!$store) {
            return redirect()->route('home')
                ->with('error', 'Toko tidak ditemukan atau belum aktif.');
        }

        $categories = Category::all();
        $brands     = Brand::all();

        return view('seller.products.create', compact('categories', 'brands', 'store'));
    }

    public function store(Request $request)
    {
        $store = $this->sellerStore();

        if (!$store) {
            return redirect()->route('home')
                ->with('error', 'Toko tidak ditemukan. Tidak bisa upload produk.');
        }

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'required|string',
            'condition_notes' => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'category_id'     => 'required|exists:categories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'size'            => 'nullable|string',
            'condition'       => 'required|in:like_new,9/10,8/10,7/10,6/10',
            'length_cm'       => 'nullable|numeric',
            'width_cm'        => 'nullable|numeric',
            'weight_gram'     => 'required|numeric|min:1',
            'gender'          => 'required|in:pria,wanita,unisex',
            'images.*'        => 'nullable|image|max:5120',
            'image_angles.*'  => 'nullable|string',
        ]);

        // ✅ Set store_id secara eksplisit
        $productData = collect($validated)
            ->except(['images', 'image_angles'])
            ->toArray();

        $productData['store_id'] = $store->id;

        // Log untuk debug
        Log::info('Creating product', [
            'store_id' => $productData['store_id'],
            'store_name' => $store->name,
            'user_id' => Auth::id(),
            'name' => $productData['name'],
        ]);

        $product = Product::create($productData);

        // Verifikasi store_id tersimpan
        Log::info('Product created', [
            'product_id' => $product->id,
            'store_id' => $product->store_id,
        ]);

        // Upload foto
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                if ($file->isValid()) {
                    $path  = $file->store('products', 'public');
                    $angle = $request->image_angles[$i] ?? 'other';
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'angle'      => $angle,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil ditambahkan ke toko ' . $store->name . '!');
    }

    public function show(Product $product)
    {
        abort_if($product->store_id !== $this->sellerStore()?->id, 403);
        return redirect()->route('seller.products.edit', $product);
    }

    public function edit(Product $product)
    {
        $store = $this->sellerStore();
        abort_if($product->store_id !== $store?->id, 403);

        $categories = Category::all();
        $brands     = Brand::all();
        $product->load('images');

        return view('seller.products.edit', compact('product', 'categories', 'brands', 'store'));
    }

    public function update(Request $request, Product $product)
    {
        $store = $this->sellerStore();
        abort_if($product->store_id !== $store?->id, 403);

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'required|string',
            'condition_notes' => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'category_id'     => 'required|exists:categories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'size'            => 'nullable|string',
            'condition'       => 'required',
            'length_cm'       => 'nullable|numeric',
            'width_cm'        => 'nullable|numeric',
            'weight_gram'     => 'required|numeric',
            'gender'          => 'required',
        ]);

        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                if ($file->isValid()) {
                    $path  = $file->store('products', 'public');
                    $angle = $request->image_angles[$i] ?? 'other';
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'angle'      => $angle,
                        'sort_order' => $product->images()->count() + $i,
                    ]);
                }
            }
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $store = $this->sellerStore();
        abort_if($product->store_id !== $store?->id, 403);

        $product->images->each(fn($img) => Storage::disk('public')->delete($img->image_path));
        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk dihapus.');
    }

    public function deleteImage(ProductImage $image)
    {
        $store   = $this->sellerStore();
        $product = $image->product;
        abort_if($product->store_id !== $store?->id, 403);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true]);
    }
}