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
            Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable();
            $table->string('raca')->nullable();
            $table->string('cor');
            $table->integer('idade');
            $table->string('sexo');
            $table->decimal('peso', 7, 2);
            $table->string('porte');
            $table->string('comportamento');
            $table->string('adestramento');
            $table->string('origem_da_raca');
            $table->string('condicoes_especiais');
            $table->string('expectativa_de_vida');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
