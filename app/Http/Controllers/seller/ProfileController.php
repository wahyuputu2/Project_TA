<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $store = Auth::user()->store;
        return view('seller.profile', compact('store'));
    }

    public function update(Request $request)
    {
        $store = Auth::user()->store;

        $request->validate([
            'name'        => 'required|string|max:100|unique:stores,name,'.$store->id,
            'description' => 'required|string|min:20',
            'phone'       => 'required|string|max:20',
            'address'     => 'required|string',
            'city'        => 'required|string',
            'province'    => 'required|string',
            'postal_code' => 'required|string',
            'bank_name'   => 'required|string',
            'bank_account_number' => 'required|string',
            'bank_account_name'   => 'required|string',
            'logo'        => 'nullable|image|max:2048',
            'banner'      => 'nullable|image|max:4096',
        ]);

        $data = $request->except(['logo','banner']);

        if ($request->hasFile('logo')) {
            if ($store->logo) Storage::disk('public')->delete($store->logo);
            $data['logo'] = $request->file('logo')->store('stores/logos','public');
        }
        if ($request->hasFile('banner')) {
            if ($store->banner) Storage::disk('public')->delete($store->banner);
            $data['banner'] = $request->file('banner')->store('stores/banners','public');
        }

        $store->update($data);

        return redirect()->back()->with('success','Profil toko berhasil diperbarui!');
    }
}