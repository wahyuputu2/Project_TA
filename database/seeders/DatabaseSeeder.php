<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user — pakai firstOrCreate agar tidak duplikat
        User::firstOrCreate(
            ['email' => 'admin@thriftstore.com'],
            [
                'name'     => 'Admin ThriftStore',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Kategori — pakai firstOrCreate agar tidak duplikat
        $categories = [
            'Kaos', 'Kemeja', 'Jaket', 'Celana',
            'Dress', 'Rok', 'Hoodie', 'Sweater', 'Outer'
        ];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        // Brand — pakai firstOrCreate agar tidak duplikat
        $brands = [
            'Uniqlo', 'H&M', 'Zara', 'Polo Ralph Lauren',
            'Tommy Hilfiger', 'Gap', 'Levi\'s', 'Nike', 'Adidas', 'Supreme'
        ];
        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand]);
        }
    }
}