<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        Product::releaseExpiredLocks();

        $filters = $request->only(['category','brand','size','condition','gender','min_price','max_price','search','sort']);
        $query = Product::with(['images','brand','category'])
            ->filter($filters);

        // Sort
        match($filters['sort'] ?? 'newest') {
            'price_asc'  => $query->orderBy('price','asc'),
            'price_desc' => $query->orderBy('price','desc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();
        $brands     = Brand::withCount('products')->get();
        $sizes      = ['XS','S','M','L','XL','XXL','XXXL','27','28','29','30','31','32','33','34','35','36','37','38','39','40'];
        $conditions = ['like_new','9/10','8/10','7/10','6/10'];

        return view('catalog.index', compact('products','categories','brands','sizes','conditions','filters'));
    }

    public function show(Product $product)
    {
        Product::releaseExpiredLocks();
        $product->load(['images','brand','category','reviews.user']);
        $related = Product::available()
            ->where('category_id', $product->category_id)
            ->where('id','!=',$product->id)
            ->limit(4)->get();

        return view('catalog.show', compact('product','related'));
    }
}