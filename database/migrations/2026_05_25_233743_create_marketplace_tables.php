<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel toko/seller
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->integer('origin_city_id')->nullable(); // untuk RajaOngkir
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->decimal('balance', 12, 2)->default(0); // saldo seller
            $table->decimal('commission_rate', 5, 2)->default(10.00); // % komisi platform
            $table->integer('total_sales')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamps();
        });

        // Tabel withdraw saldo seller
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'processed', 'rejected'])->default('pending');
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_name');
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // Tabel komisi per transaksi
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->decimal('order_amount', 12, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->decimal('seller_amount', 12, 2);
            $table->enum('status', ['pending', 'settled', 'refunded'])->default('pending');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });

        // Tambah kolom store_id ke products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Tambah kolom store_id ke orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        // Tambah role seller ke users
        // (ALTER enum)
        \DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin','admin','seller','customer') DEFAULT 'customer'");
    }

    public function down(): void
    {
        Schema::table('orders', fn($t) => $t->dropForeign(['store_id']));
        Schema::table('products', fn($t) => $t->dropForeign(['store_id']));
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('stores');
    }
};