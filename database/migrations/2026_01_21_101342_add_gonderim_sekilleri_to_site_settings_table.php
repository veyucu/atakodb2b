<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * JSON format: [{"aciklama": "Kargo ile", "erp_aciklama": "KARGO"}, ...]
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('gonderim_sekilleri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('gonderim_sekilleri');
        });
    }
};
