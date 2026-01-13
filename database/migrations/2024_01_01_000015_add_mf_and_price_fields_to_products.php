<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // MF bilgisi (string - örn: "10+5", "20+8" gibi)
            $table->string('mf')->nullable()->after('ticari_iskonto');
            
            // Depocu Fiyatı
            $table->decimal('depocu_fiyati', 10, 2)->nullable()->after('mf');
            
            // Net Fiyat (manuel girilebilir)
            $table->decimal('net_fiyat_manuel', 10, 2)->nullable()->after('depocu_fiyati');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['mf', 'depocu_fiyati', 'net_fiyat_manuel']);
        });
    }
};


















