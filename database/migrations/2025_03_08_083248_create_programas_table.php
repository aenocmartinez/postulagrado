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
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->integer('codigo');
            $table->integer('snies')->nullable();
            $table->unsignedBigInteger('metodologia_id');
            $table->unsignedBigInteger('nivel_educativo_id');
            $table->unsignedBigInteger('modalidad_id');
            $table->unsignedBigInteger('jornada_id');
            $table->unsignedBigInteger('unidad_regional_id');
            $table->timestamps();

            $table->foreign('metodologia_id')->references('id')->on('metodologias')->onDelete('restrict');
            $table->foreign('nivel_educativo_id')->references('id')->on('nivel_educativo')->onDelete('restrict');
            $table->foreign('modalidad_id')->references('id')->on('modalidades')->onDelete('restrict');
            $table->foreign('jornada_id')->references('id')->on('jornadas')->onDelete('restrict');
            $table->foreign('unidad_regional_id')->references('id')->on('unidad_regional')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
