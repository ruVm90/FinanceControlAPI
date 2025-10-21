<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_expenses_endpoint_exists(): void
    {
        // Peticion
        $response = $this->getJson('/api/expenses');

        // Verifico que no responda 404
        $response->assertStatus(401); // requiere auth
    }

    public function test_authenticated_user_can_access_expenses(): void
    {
        // Creo un usuario de prueba
        $user = User::factory()->create();

        // Autentico con Sanctum
        $response = $this->actingAs($user, 'sanctum')
                    ->getJson('/api/expenses');
        // Verifico la respuesta
        $response->assertStatus(200);            
    }
}
