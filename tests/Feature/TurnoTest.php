<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Turno;
use Mockery;

class TurnoTest extends TestCase
{
    use RefreshDatabase; // IMPORTANTE: Descomenta esta línea
    
    /**
     * Test TP-001: Flujo normal exitoso
     * Cobertura: Usuario autenticado, sin turno activo, base válida
     */
    public function test_abrir_turno_exitoso()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Verificar que no hay turnos antes
        $this->assertDatabaseCount('turnos', 0);
        
        // Act
        $response = $this->post(route('turnos.abrir'), [
            'base' => 100000
        ]);
        
        // Assert
        $response->assertRedirect(route('turno.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('turnos', [
            'user_id' => $user->id,
            'estado' => 'activo',
            'base' => 100000
        ]);
    }
    
    /**
     * Test TP-002: Usuario NO autenticado
     * Cobertura: Sesión no iniciada, intento de acceso
     */
    public function test_usuario_no_autenticado_no_puede_abrir_turno()
    {
        // Act - Sin autenticar
        $response = $this->post(route('turnos.abrir'), [
            'base' => 100000
        ]);
        
        // Assert - Debería redirigir al login
        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('turnos', 0);
    }
    
    /**
     * Test TP-004: Base inválida - valor negativo
     * Cobertura: Validación de base negativa
     */
    public function test_no_puede_abrir_turno_con_base_negativa()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Act
        $response = $this->post(route('turnos.abrir'), [
            'base' => -100
        ]);
        
        // Assert
        $response->assertSessionHasErrors(['base']);
        $this->assertDatabaseCount('turnos', 0);
    }
    
    /**
     * Test TP-005: Base inválida - valor no numérico
     * Cobertura: Validación de tipo de dato
     */
    public function test_no_puede_abrir_turno_con_base_no_numerica()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Act
        $response = $this->post(route('turnos.abrir'), [
            'base' => 'cien mil'
        ]);
        
        // Assert
        $response->assertSessionHasErrors(['base']);
        $this->assertDatabaseCount('turnos', 0);
    }
    
    /**
     * Test TP-006: Base requerida
     * Cobertura: Validación de campo requerido
     */
    public function test_base_es_requerida()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Act
        $response = $this->post(route('turnos.abrir'), []);
        
        // Assert
        $response->assertSessionHasErrors(['base']);
        $this->assertDatabaseCount('turnos', 0);
    }
    
    /**
     * Limpiar Mockery después de cada prueba
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}