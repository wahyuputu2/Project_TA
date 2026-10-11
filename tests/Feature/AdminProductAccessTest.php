<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $seller;
    private User $customer;
    private Store $store;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->seller = User::factory()->create(['role' => 'seller']);
        $this->customer = User::factory()->create(['role' => 'customer']);

        $this->store = Store::create([
            'user_id' => $this->seller->id,
            'name' => 'Vintage Vault',
            'slug' => 'vintage-vault',
            'status' => 'active',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
        ]);

        $category = Category::create([
            'name' => 'Denim',
            'slug' => 'denim',
        ]);

        $brand = Brand::create([
            'name' => 'Levi\'s',
            'slug' => 'levis',
        ]);

        $this->product = Product::create([
            'store_id' => $this->store->id,
            'name' => 'Vintage 501 Selvedge',
            'slug' => 'vintage-501-selvedge',
            'description' => 'Classic raw denim jeans.',
            'price' => 450000,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'size' => '32',
            'condition' => '9/10',
            'weight_gram' => 600,
            'status' => 'available',
        ]);
    }

    public function test_admin_can_view_products_index_with_search_and_filters(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.index', [
            'search' => 'Vintage',
            'status' => 'available',
            'store_id' => $this->store->id,
        ]));

        $response->assertOk();
        $response->assertSee('Vintage 501 Selvedge');
        $response->assertSee('Vintage Vault');
        $response->assertSee('Detail');
        // Pastikan tidak ada tombol Edit dan Hapus pada tabel
        $response->assertDontSee('admin.products.edit');
        $response->assertDontSee('admin.products.destroy');
    }

    public function test_admin_can_view_product_detail_including_seller_info(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.show', $this->product));

        $response->assertOk();
        $response->assertSee($this->product->name);
        $response->assertSee('Rp 450.000');
        $response->assertSee('Vintage Vault');
        $response->assertSee($this->seller->name);
        $response->assertSee('Bandung');
        $response->assertSee('Mode Pemantauan (Read-Only)');
    }

    public function test_admin_cannot_access_product_creation_or_editing(): void
    {
        // Route create dan edit sudah tidak terdaftar untuk Admin (404 Not Found)
        $createResponse = $this->actingAs($this->admin)->get('/admin/products/create');
        $this->assertContains($createResponse->status(), [403, 404]);

        $storeResponse = $this->actingAs($this->admin)->post('/admin/products', [
            'name' => 'Illegal Product',
        ]);
        $this->assertContains($storeResponse->status(), [403, 404, 405]);

        $editResponse = $this->actingAs($this->admin)->get('/admin/products/' . $this->product->id . '/edit');
        $this->assertContains($editResponse->status(), [403, 404]);

        $deleteResponse = $this->actingAs($this->admin)->delete('/admin/products/' . $this->product->id);
        $this->assertContains($deleteResponse->status(), [403, 404, 405]);
    }

    public function test_customer_cannot_access_admin_products(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.products.index'));
        $this->assertContains($response->status(), [403, 302]);
    }
}
