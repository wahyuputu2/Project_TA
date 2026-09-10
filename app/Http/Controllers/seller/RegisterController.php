<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        // Cek sudah punya toko aktif
        if (Auth::user()->store && Auth::user()->store->isActive()) {
            return redirect()->route('seller.dashboard');
        }
        return view('seller.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:100|unique:stores,name',
            'description'         => 'required|string|min:10',
            'phone'               => 'required|string|max:20',
            'address'             => 'required|string',
            'city'                => 'required|string',
            'province'            => 'required|string',
            'postal_code'         => 'required|string',
            'bank_name'           => 'required|string',
            'bank_account_number' => 'required|string',
            'bank_account_name'   => 'required|string',
            'logo'                => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['logo', '_token']);
        $data['user_id'] = Auth::id();
        $data['status']  = 'pending';

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        $store = Store::create($data);

        // Update role user jadi seller
        Auth::user()->update(['role' => 'seller']);

        return redirect()->route('home')
            ->with('success', 'Pendaftaran toko "' . $store->name . '" berhasil! Tunggu verifikasi admin dalam 1x24 jam.');
    }
}