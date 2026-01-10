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
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Usuario que abre el turno
            $table->decimal('base', 10, 2)->default(0); // Dinero inicial
            $table->decimal('ventas_totales', 10, 2)->default(0);
            $table->decimal('efectivo', 10, 2)->default(0);
            $table->decimal('transferencia', 10, 2)->default(0);
            $table->timestamp('inicio')->useCurrent();
            $table->timestamp('cierre')->nullable();
            $table->enum('estado', ['activo', 'cerrado'])->default('activo');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};
