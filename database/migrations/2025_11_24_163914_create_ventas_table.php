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
            Schema::create('ventas', function (Blueprint $table) {
                $table->id();
                $table->string('numero_factura')->unique()->nullable();
                $table->unsignedBigInteger('cliente_id'); // Relación con cliente
                $table->unsignedBigInteger('turno_id')->nullable();
                $table->string('metodo_pago');
                $table->decimal('total', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
                $table->foreign('turno_id')->references('id')->on('turnos')->onDelete('set null');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('ventas');
        }
    };
