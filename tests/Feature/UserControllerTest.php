<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Mockery;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario administrador para las pruebas
        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'must_change_password' => false
        ]);
    }

    /**
     * Test TP-001: Reset exitoso - Usuario autenticado
     * Cobertura: Usuario existente, must_change_password cambia a true
     */
    public function test_administrador_puede_resetear_password()
    {
        // Arrange
        $usuario = User::factory()->create([
            'must_change_password' => false,
            'email' => 'usuario@test.com'
        ]);

        // Act - Autenticado como admin
        $response = $this->actingAs($this->admin)
                         ->post(route('usuarios.reset-password', $usuario->id));

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Contraseña reseteada exitosamente. El usuario deberá establecer una nueva contraseña al iniciar sesión.',
                'email' => $usuario->email
            ]);

        // Verificar BD
        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'must_change_password' => true
        ]);
    }

    /**
     * Test TP-002: Usuario NO autenticado intenta resetear
     * Cobertura: Redirección por falta de autenticación
     */
    public function test_usuario_no_autenticado_no_puede_resetear()
    {
        // Arrange
        $usuario = User::factory()->create();

        // Act - SIN autenticar
        $response = $this->post(route('usuarios.reset-password', $usuario->id));

        // Assert - Debe redirigir al login (302)
        $response->assertStatus(302);
        // Verificar que redirige a login (típico de Breeze)
        $response->assertRedirect('/login');
        
        // Verificar que NO se actualizó la BD
        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'must_change_password' => false
        ]);
    }

    /**
     * Test TP-003: Usuario NO existente
     * Cobertura: ID inválido, findOrFail lanza excepción
     */
    public function test_reset_password_usuario_no_existente()
    {
        // Arrange
        $nonExistentId = 99999;

        // Act - Autenticado
        $response = $this->actingAs($this->admin)
                         ->post(route('usuarios.reset-password', $nonExistentId));

        // Assert
        $response->assertStatus(500)
            ->assertJson([
                'success' => false
            ]);

        // Verificar mensaje de error
        $this->assertStringContainsString(
            'No query results for model',
            $response->json('message')
        );
    }

    /**
     * Test TP-004: Usuario con must_change_password ya en true
     * Cobertura: Actualizar cuando ya está en true
     */
    public function test_reset_password_cuando_ya_esta_en_true()
    {
        // Arrange
        $usuario = User::factory()->create([
            'must_change_password' => true
        ]);

        // Act - Autenticado
        $response = $this->actingAs($this->admin)
                         ->post(route('usuarios.reset-password', $usuario->id));

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        // Debe mantener true
        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'must_change_password' => true
        ]);
    }

    /**
     * Test TP-005: Verificar logs
     * Cobertura: Registro de logs antes y después
     */
    public function test_reset_password_registra_logs()
    {
        // Arrange
        $usuario = User::factory()->create();

        // Mock de Log
        Log::shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context) use ($usuario) {
                return (str_contains($message, 'Reset password for user:') || 
                        str_contains($message, 'After reset - must_change_password:')) &&
                       isset($context['id']) && 
                       $context['id'] == $usuario->id;
            });

        // Act - Autenticado
        $response = $this->actingAs($this->admin)
                         ->post(route('usuarios.reset-password', $usuario->id));

        // Assert
        $response->assertStatus(200);
    }

    /**
     * Test TP-009: Verificar estructura JSON
     */
    public function test_estructura_respuesta_exitosa()
    {
        // Arrange
        $usuario = User::factory()->create();

        // Act - Autenticado
        $response = $this->actingAs($this->admin)
                         ->post(route('usuarios.reset-password', $usuario->id));

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'email'
            ]);
    }

    /**
     * Test TP-010: Verificar email en respuesta
     */
    public function test_respuesta_incluye_email_correcto()
    {
        // Arrange
        $email = 'cliente-especifico@test.com';
        $usuario = User::factory()->create([
            'email' => $email
        ]);

        // Act - Autenticado
        $response = $this->actingAs($this->admin)
                         ->post(route('usuarios.reset-password', $usuario->id));

        // Assert
        $response->assertStatus(200);
        $this->assertEquals($email, $response->json('email'));
    }

    /**
     * Limpiar Mockery
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}