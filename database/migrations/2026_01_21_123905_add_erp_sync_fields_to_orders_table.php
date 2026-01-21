<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('erp_synced')->default(false)->after('gonderim_sekli');
            $table->timestamp('erp_synced_at')->nullable()->after('erp_synced');
            $table->string('erp_order_number', 50)->nullable()->after('erp_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['erp_synced', 'erp_synced_at', 'erp_order_number']);
        });
    }
};
