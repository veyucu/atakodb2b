<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * - Remove: product_code, product_name, birim_maliyet
     * - Rename: net_price -> net_fiyat
     * - Change: mal_fazlasi to string
     */
    public function up(): void
    {
        // Use raw SQL for MySQL compatibility
        DB::statement('ALTER TABLE order_items DROP COLUMN IF EXISTS product_code');
        DB::statement('ALTER TABLE order_items DROP COLUMN IF EXISTS product_name');
        DB::statement('ALTER TABLE order_items DROP COLUMN IF EXISTS birim_maliyet');

        // Check if net_price exists and rename to net_fiyat
        if (Schema::hasColumn('order_items', 'net_price')) {
            DB::statement('ALTER TABLE order_items CHANGE net_price net_fiyat DECIMAL(10,2) NULL');
        }

        // Change mal_fazlasi to varchar
        if (Schema::hasColumn('order_items', 'mal_fazlasi')) {
            DB::statement('ALTER TABLE order_items MODIFY mal_fazlasi VARCHAR(50) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change mal_fazlasi back to integer
        if (Schema::hasColumn('order_items', 'mal_fazlasi')) {
            DB::statement('ALTER TABLE order_items MODIFY mal_fazlasi INT NULL DEFAULT 0');
        }

        // Rename back
        if (Schema::hasColumn('order_items', 'net_fiyat')) {
            DB::statement('ALTER TABLE order_items CHANGE net_fiyat net_price DECIMAL(10,2) NULL');
        }

        // Re-add removed columns
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_code')) {
                $table->string('product_code')->nullable();
            }
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable();
            }
            if (!Schema::hasColumn('order_items', 'birim_maliyet')) {
                $table->decimal('birim_maliyet', 10, 2)->nullable();
            }
        });
    }
};
