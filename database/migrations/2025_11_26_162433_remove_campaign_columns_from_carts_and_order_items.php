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
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'product_campaign_id')) {
                $table->dropForeign(['product_campaign_id']);
                $table->dropColumn('product_campaign_id');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_campaign_id')) {
                $table->dropForeign(['product_campaign_id']);
                $table->dropColumn('product_campaign_id');
            }
            if (Schema::hasColumn('order_items', 'campaign_name')) {
                $table->dropColumn('campaign_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('product_campaign_id')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_campaign_id')->nullable();
            $table->string('campaign_name')->nullable();
        });
    }
};
