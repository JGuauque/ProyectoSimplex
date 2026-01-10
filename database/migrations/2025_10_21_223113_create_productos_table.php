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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->decimal('costo', 10, 2);
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('categoria'); // Son Tecnologia, Hogar, Jugueteria, Salud, Cocina.
            $table->boolean('destacado')->default(false);
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
