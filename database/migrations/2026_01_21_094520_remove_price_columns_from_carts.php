<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Sepet fiyatları artık dinamik hesaplanacak
     */
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['price', 'net_price', 'mal_fazlasi', 'birim_maliyet', 'bonus_option']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('net_price', 10, 2)->nullable();
            $table->integer('mal_fazlasi')->default(0);
            $table->decimal('birim_maliyet', 10, 2)->nullable();
            $table->tinyInteger('bonus_option')->default(1);
        });
    }
};
