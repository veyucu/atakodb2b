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
            $table->dropColumn(['mf', 'net_fiyat_manuel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('mf')->nullable()->after('ticari_iskonto');
            $table->decimal('net_fiyat_manuel', 10, 2)->nullable()->after('depocu_fiyati');
        });
    }
};
