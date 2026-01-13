<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Products tablosu
        DB::statement('ALTER TABLE products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci');
        
        // Users tablosu
        DB::statement('ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci');
        
        // Orders tablosu
        DB::statement('ALTER TABLE orders CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci');
        
        // Order items tablosu
        DB::statement('ALTER TABLE order_items CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci');
        
        // Carts tablosu
        DB::statement('ALTER TABLE carts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci');
        
        // Sliders tablosu
        DB::statement('ALTER TABLE sliders CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci');
        
        // Settings tablosu
        DB::statement('ALTER TABLE settings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci');
        
        // Site settings tablosu
        DB::statement('ALTER TABLE site_settings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci');
        
        // Customer activities tablosu
        DB::statement('ALTER TABLE customer_activities CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eski collation'a geri dön
        DB::statement('ALTER TABLE products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE orders CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE order_items CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE carts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE sliders CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE settings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE site_settings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE customer_activities CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }
};
