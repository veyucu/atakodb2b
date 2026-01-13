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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('urun_kodu')->unique();
            $table->string('urun_adi');
            $table->string('barkod')->nullable();
            $table->decimal('satis_fiyati', 10, 2)->default(0);
            $table->decimal('kdv_orani', 5, 2)->default(0);
            $table->string('marka')->nullable();
            $table->string('grup')->nullable();
            $table->string('kod1')->nullable();
            $table->string('kod2')->nullable();
            $table->string('kod3')->nullable();
            $table->string('kod4')->nullable();
            $table->string('kod5')->nullable();
            $table->string('urun_resmi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};




















