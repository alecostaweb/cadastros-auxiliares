<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos_graduacao', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('codcur')->unique();
            $table->unsignedInteger('codset')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos_graduacao');
    }
};
