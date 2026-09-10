<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Store;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Admin melihat SEMUA produk dari semua toko
        $products = Product::with(['category','brand','images','store'])
            ->when($request->search, fn($q) => $q->where('name','like','%'.$request->search.'%'))
            ->when($request->status, fn($q) => $q->where('status',$request->status))
            ->when($request->store_id, fn($q) => $q->where('store_id',$request->store_id))
            ->latest()
            ->paginate(20);

        $stores = Store::where('status','active')->get(['id','name']);

        return view('admin.products.index', compact('products','stores'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands     = Brand::all();
        $stores     = Store::where('status','active')->get();
        return view('admin.products.create', compact('categories','brands','stores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'required|string',
            'condition_notes' => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'category_id'     => 'required|exists:categories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'store_id'        => 'nullable|exists:stores,id',
            'size'            => 'nullable|string',
            'condition'       => 'required|in:like_new,9/10,8/10,7/10,6/10',
            'length_cm'       => 'nullable|numeric',
            'width_cm'        => 'nullable|numeric',
            'weight_gram'     => 'required|numeric|min:1',
            'gender'          => 'required|in:pria,wanita,unisex',
            'images.*'        => 'nullable|image|max:5120',
            'image_angles.*'  => 'nullable|string',
        ]);

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
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

        return redirect()->route('admin.products.index')
            ->with('success','Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands     = Brand::all();
        $stores     = Store::where('status','active')->get();
        $product->load('images');
        return view('admin.products.edit', compact('product','categories','brands','stores'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'required|string',
            'condition_notes' => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'category_id'     => 'required|exists:categories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'store_id'        => 'nullable|exists:stores,id',
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

        return redirect()->route('admin.products.index')
            ->with('success','Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $product->images->each(fn($img) => Storage::disk('public')->delete($img->image_path));
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success','Produk dihapus.');
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return response()->json(['success' => true]);
    }
}