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
        Schema::create('processed_records', function (Blueprint $table) {
            $table->id();
            $table->date('data_registro')->nullable();
            $table->decimal('metrica_a', 20, 4)->nullable();
            $table->decimal('metrica_b', 20, 4)->nullable();
            $table->decimal('indicador_x', 20, 4)->nullable();
            $table->decimal('indicador_y', 20, 4)->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processed_records');
    }
};
