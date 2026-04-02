<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cursos_graduacao', function (Blueprint $table) {
            $table->string('nomcur')->nullable()->after('codcur');
            $table->string('nomset')->nullable()->after('codset');
            $table->string('nomabvset', 100)->nullable()->after('nomset');
        });
    }

    public function down(): void
    {
        Schema::table('cursos_graduacao', function (Blueprint $table) {
            $table->dropColumn(['nomcur', 'nomset', 'nomabvset']);
        });
    }
};
