<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregar campos sin unique primero
            $table->string('apellidos')->nullable()->after('name');
            $table->string('identificacion')->nullable()->after('apellidos');
            $table->string('username')->nullable()->after('email');
            $table->enum('rol', ['Owner', 'Administrador', 'Vendedor'])->default('Vendedor')->after('username');
        });

        // Actualizar los usuarios existentes con valores únicos
        DB::table('users')->whereNull('identificacion')->update([
            'identificacion' => DB::raw("CONCAT('ID_', id, '_', UNIX_TIMESTAMP())"),
            'username' => DB::raw("CONCAT('user_', id)")
        ]);

        // Ahora hacer los campos únicos
        Schema::table('users', function (Blueprint $table) {
            $table->string('identificacion')->nullable(false)->unique()->change();
            $table->string('username')->nullable(false)->unique()->change();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropUnique(['identificacion']);
            $table->dropUnique(['username']);
            $table->dropColumn(['apellidos', 'identificacion', 'username', 'rol']);
        });
    }
};
