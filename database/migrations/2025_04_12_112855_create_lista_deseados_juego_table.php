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
        Schema::create('lista_deseados_juego', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lista_deseado_id")->constrained()->cascadeOnDelete();
            $table->foreignId("juego_id")->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_deseados_juego');
    }
};
