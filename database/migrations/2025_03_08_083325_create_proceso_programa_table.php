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
        Schema::create('proceso_programa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proceso_id');
            $table->unsignedBigInteger('programa_id');
            $table->timestamps();

            $table->unique(['proceso_id', 'programa_id']);
            $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('restrict');
            $table->foreign('programa_id')->references('id')->on('programas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceso_programa');
    }
};
