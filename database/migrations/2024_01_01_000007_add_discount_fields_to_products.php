<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('kurum_iskonto', 5, 2)->default(0)->after('satis_fiyati')->comment('Kurum İskontosu %');
            $table->decimal('eczaci_kari', 5, 2)->default(0)->after('kurum_iskonto')->comment('Eczacı Karı %');
            $table->decimal('ticari_iskonto', 5, 2)->default(0)->after('eczaci_kari')->comment('Ticari İskonto %');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['kurum_iskonto', 'eczaci_kari', 'ticari_iskonto']);
        });
    }
};


















