<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Bonus opsiyonları - mf1: "10+1", mf2: "15+5" gibi
            $table->string('mf1')->nullable()->after('mf');
            $table->string('mf2')->nullable()->after('mf1');
            // Bonus opsiyonlarına karşılık gelen net fiyatlar
            $table->decimal('net_fiyat1', 10, 2)->nullable()->after('net_fiyat_manuel');
            $table->decimal('net_fiyat2', 10, 2)->nullable()->after('net_fiyat1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['mf1', 'mf2', 'net_fiyat1', 'net_fiyat2']);
        });
    }
};
