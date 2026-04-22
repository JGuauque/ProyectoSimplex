<?php
// tests/Feature/PrestamoControllerTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Prestamo;
use App\Models\Producto;
use App\Models\LocalAliado;
use Illuminate\Support\Facades\DB;

class PrestamoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $vendedor;
    protected $local;
    protected $producto;
    protected $prestamoPrestado;
    protected $prestamoDevuelto;
    protected $prestamoPagado;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario para autenticación
        $this->vendedor = User::factory()->create();

        // Crear local aliado
        $this->local = LocalAliado::factory()->create();

        // Crear productos
        $this->producto = Producto::factory()->create([
            'nombre' => 'Producto Test',
            'codigo' => 'PROD-TEST-001',
            'costo' => 25000,
            'precio' => 50000,
            'stock' => 100,
            'categoria' => 'Tecnologia'
        ]);

        $productoDevuelto = Producto::factory()->create([
            'nombre' => 'Producto Devuelto',
            'codigo' => 'PROD-DEV-001',
            'costo' => 15000,
            'precio' => 30000,
            'stock' => 50,
            'categoria' => 'Jugueteria'
        ]);

        $productoPagado = Producto::factory()->create([
            'nombre' => 'Producto Pagado',
            'codigo' => 'PROD-PAG-001',
            'costo' => 10000,
            'precio' => 20000,
            'stock' => 30,
            'categoria' => 'Hogar'
        ]);

        // NOTA: Como el ENUM solo acepta 'Prestado', 'Devuelto', 'Pago'
        // NO podemos usar 'Pendiente'
        
        // Prestado - para probar transiciones desde Prestado
        $this->prestamoPrestado = Prestamo::create([
            'local_id' => $this->local->id,
            'producto_id' => $this->producto->id,
            'cantidad' => 3,
            'precio_unitario' => 50000,
            'subtotal' => 150000,
            'fecha_prestamo' => now(),
            'estado' => 'Prestado' // Valor válido del ENUM
        ]);

        // Devuelto - para probar transiciones desde Devuelto
        $this->prestamoDevuelto = Prestamo::create([
            'local_id' => $this->local->id,
            'producto_id' => $productoDevuelto->id,
            'cantidad' => 2,
            'precio_unitario' => 30000,
            'subtotal' => 60000,
            'fecha_prestamo' => now(),
            'estado' => 'Devuelto' // Valor válido del ENUM
        ]);

        // Pago - para probar transiciones desde Pago (estado final)
        $this->prestamoPagado = Prestamo::create([
            'local_id' => $this->local->id,
            'producto_id' => $productoPagado->id,
            'cantidad' => 1,
            'precio_unitario' => 20000,
            'subtotal' => 20000,
            'fecha_prestamo' => now(),
            'estado' => 'Pago' // Valor válido del ENUM
        ]);
    }

    /**
     * Test 1: Transición válida: Prestado → Devuelto
     * - INCREMENTA stock
     */
    public function test_transicion_prestado_a_devuelto_incrementa_stock()
    {
        $prestamo = $this->prestamoPrestado;
        $producto = Producto::find($prestamo->producto_id);
        $stockAntes = $producto->stock;
        $cantidad = $prestamo->cantidad;

        $response = $this->actingAs($this->vendedor)
            ->putJson("/prestamos/{$prestamo->id}/estado", [
                'estado' => 'Devuelto'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Estado actualizado exitosamente'
            ]);

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => 'Devuelto'
        ]);

        $this->assertEquals($stockAntes + $cantidad, $producto->fresh()->stock);
    }

    /**
     * Test 2: Transición válida: Devuelto → Pago
     * - DECREMENTA stock
     */
    public function test_transicion_devuelto_a_pago_decrementa_stock()
    {
        $prestamo = $this->prestamoDevuelto;
        $producto = Producto::find($prestamo->producto_id);
        $stockAntes = $producto->stock;
        $cantidad = $prestamo->cantidad;

        $response = $this->actingAs($this->vendedor)
            ->putJson("/prestamos/{$prestamo->id}/estado", [
                'estado' => 'Pago'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Estado actualizado exitosamente'
            ]);

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => 'Pago'
        ]);

        $this->assertEquals($stockAntes - $cantidad, $producto->fresh()->stock);
    }

    /**
     * Test 3: Transición válida: Prestado → Pago
     * - No cambia stock
     */
    public function test_transicion_prestado_a_pago_no_cambia_stock()
    {
        $prestamo = $this->prestamoPrestado;
        $producto = Producto::find($prestamo->producto_id);
        $stockAntes = $producto->stock;

        $response = $this->actingAs($this->vendedor)
            ->putJson("/prestamos/{$prestamo->id}/estado", [
                'estado' => 'Pago'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Estado actualizado exitosamente'
            ]);

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => 'Pago'
        ]);

        $this->assertEquals($stockAntes, $producto->fresh()->stock);
    }

    /**
     * Test 4: Transición INVÁLIDA: Devuelto → Prestado
     * - Retorna error 422
     */
    public function test_transicion_invalida_devuelto_a_prestado()
    {
        $prestamo = $this->prestamoDevuelto;
        $estadoAnterior = $prestamo->estado;

        $response = $this->actingAs($this->vendedor)
            ->putJson("/prestamos/{$prestamo->id}/estado", [
                'estado' => 'Prestado'
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);

        $this->assertStringContainsString(
            "Transición no permitida: de 'Devuelto' a 'Prestado'",
            $response->json('message')
        );

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => $estadoAnterior
        ]);
    }

    /**
     * Test 5: Transición INVÁLIDA: Pago → Devuelto
     * - Retorna error 422
     */
    public function test_transicion_invalida_pago_a_devuelto()
    {
        $prestamo = $this->prestamoPagado;
        $estadoAnterior = $prestamo->estado;

        $response = $this->actingAs($this->vendedor)
            ->putJson("/prestamos/{$prestamo->id}/estado", [
                'estado' => 'Devuelto'
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);

        $this->assertStringContainsString(
            "Transición no permitida: de 'Pago' a 'Devuelto'",
            $response->json('message')
        );

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => $estadoAnterior
        ]);
    }

    /**
     * Test 6: Transición INVÁLIDA: Pago → Prestado
     * - Retorna error 422
     */
    public function test_transicion_invalida_pago_a_prestado()
    {
        $prestamo = $this->prestamoPagado;
        $estadoAnterior = $prestamo->estado;

        $response = $this->actingAs($this->vendedor)
            ->putJson("/prestamos/{$prestamo->id}/estado", [
                'estado' => 'Prestado'
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);

        $this->assertStringContainsString(
            "Transición no permitida: de 'Pago' a 'Prestado'",
            $response->json('message')
        );

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => $estadoAnterior
        ]); 
    }
}