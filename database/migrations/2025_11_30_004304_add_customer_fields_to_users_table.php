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
        Schema::table('users', function (Blueprint $table) {
            // Müşteri bilgileri alanlarını ekle
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('musteri_kodu')->nullable()->unique()->after('username');
            $table->string('musteri_adi')->nullable()->after('musteri_kodu');
            $table->text('adres')->nullable()->after('musteri_adi');
            $table->string('ilce')->nullable()->after('adres');
            $table->string('il')->nullable()->after('ilce');
            $table->string('gln_numarasi')->nullable()->after('il');
            $table->string('telefon')->nullable()->after('gln_numarasi');
            $table->string('mail_adresi')->nullable()->after('telefon');
            $table->string('vergi_dairesi')->nullable()->after('mail_adresi');
            $table->string('vergi_kimlik_numarasi')->nullable()->after('vergi_dairesi');
            $table->string('grup_kodu')->nullable()->after('vergi_kimlik_numarasi');
            $table->string('kod1')->nullable()->after('grup_kodu');
            $table->string('kod2')->nullable()->after('kod1');
            $table->string('kod3')->nullable()->after('kod2');
            $table->string('kod4')->nullable()->after('kod3');
            $table->string('kod5')->nullable()->after('kod4');
        });

        // Mevcut customer verilerini users tablosuna taşı
        \DB::statement('
            UPDATE users u
            INNER JOIN customers c ON u.customer_id = c.id
            SET 
                u.username = c.musteri_kodu,
                u.musteri_kodu = c.musteri_kodu,
                u.musteri_adi = c.musteri_adi,
                u.adres = c.adres,
                u.ilce = c.ilce,
                u.il = c.il,
                u.gln_numarasi = c.gln_numarasi,
                u.telefon = c.telefon,
                u.mail_adresi = c.mail_adresi,
                u.vergi_dairesi = c.vergi_dairesi,
                u.vergi_kimlik_numarasi = c.vergi_kimlik_numarasi,
                u.grup_kodu = c.grup_kodu,
                u.kod1 = c.kod1,
                u.kod2 = c.kod2,
                u.kod3 = c.kod3,
                u.kod4 = c.kod4,
                u.kod5 = c.kod5
            WHERE u.customer_id IS NOT NULL
        ');

        // customer_id foreign key ve column'ı kaldır
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // customer_id'yi geri ekle
            $table->foreignId('customer_id')->nullable()->after('user_type')->constrained('customers')->onDelete('cascade');
            
            // Müşteri alanlarını kaldır
            $table->dropColumn([
                'username',
                'musteri_kodu',
                'musteri_adi',
                'adres',
                'ilce',
                'il',
                'gln_numarasi',
                'telefon',
                'mail_adresi',
                'vergi_dairesi',
                'vergi_kimlik_numarasi',
                'grup_kodu',
                'kod1',
                'kod2',
                'kod3',
                'kod4',
                'kod5',
            ]);
        });
    }
};
