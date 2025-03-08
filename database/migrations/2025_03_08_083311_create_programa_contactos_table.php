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
        Schema::create('programa_contactos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('telefono', 40);
            $table->string('email', 255);
            $table->string('observacion', 2500)->nullable();
            $table->unsignedBigInteger('programa_id');

            $table->foreign('programa_id')->references('id')->on('programas')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programa_contactos');
    }
};
