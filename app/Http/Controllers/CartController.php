<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        $items = $this->cart->getItems();
        $total = $this->cart->total();
        return view('cart.index', compact('items','total'));
    }

    public function add(Product $product)
    {
        $result = $this->cart->add($product);
        return redirect()->back()
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function remove(Product $product)
    {
        $this->cart->remove($product);
        return redirect()->route('cart.index')->with('success','Produk dihapus dari keranjang.');
    }
}