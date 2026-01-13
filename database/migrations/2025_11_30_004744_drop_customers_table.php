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
        // Önce orders tablosundaki customer_id foreign key'ini kaldır
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
        
        // Sonra customers tablosunu sil
        Schema::dropIfExists('customers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('musteri_kodu')->unique();
            $table->string('musteri_adi');
            $table->text('adres')->nullable();
            $table->string('ilce')->nullable();
            $table->string('il')->nullable();
            $table->string('gln_numarasi')->nullable();
            $table->string('telefon')->nullable();
            $table->string('mail_adresi')->nullable();
            $table->string('vergi_dairesi')->nullable();
            $table->string('vergi_kimlik_numarasi')->nullable();
            $table->string('grup_kodu')->nullable();
            $table->string('kod1')->nullable();
            $table->string('kod2')->nullable();
            $table->string('kod3')->nullable();
            $table->string('kod4')->nullable();
            $table->string('kod5')->nullable();
            $table->string('plasiyer_kodu')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
        });
    }
};
