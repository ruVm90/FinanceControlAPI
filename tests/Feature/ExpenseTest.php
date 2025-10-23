<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;
    private Category $category;
    
    /**
     * Setup que se ejecuta ANTES de cada test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear una categoría que todos los tests pueden usar
        $this->category = Category::factory()->create();
    }

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

    public function test_user_can_create_expense_succesfully(): void
    {

        $user = User::factory()->create();
        $category = $this->category->id;

        $expenseData = [
            'title' => 'Almuerzo de negocios',
            'description' => 'Reunion muy importante',
            'amount' => 45.50,
            'category_id' => $category,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/expenses', $expenseData);

        $response->assertCreated();
        $response->assertJson([
            'data' => [
                'title' => 'Almuerzo de negocios',
                'amount' => 45.50
            ]
        ]);
        $this->assertDatabaseHas('expenses', [
            'title' => 'Almuerzo de negocios',
            'description' => 'Reunion muy importante',
            'amount' => 45.50,
            'category_id' => $category,
        ]);
    }

    public function test_user_can_update_their_expense(): void
    {
        $user = User::factory()->create();
        $category = $this->category->id;
        $expense = Expense::factory()->for($user)->create([
            'title' => 'Original',
            'amount' => 50.00,
            'category_id' => $category
        ]);

        $updatedData = [
            'title' => 'Updated',
            'amount' => 100.00
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/expenses/{$expense->id}", $updatedData);

        $response->assertOk();

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'title' => 'Updated',
            'amount' => 100.00
        ]);

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
            'title' => 'Original'
        ]);
    }
    public function test_user_can_delete_their_expense(): void
    {
        $user = User::factory()->create();
        $category = $this->category->id;
        $expense = Expense::factory()->for($user)->create([
            'title' => 'Gasto a borrar',
            'amount' => 50.00,
            'category_id' => $category
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/expenses/{$expense->id}");

        $response->assertOk();
        $this->assertDatabaseMissing(
            'expenses',
            [
                'id' => $expense->id
            ]
        );
    }

    // TESTING DE VALIDACIONES
    public function test_title_is_required_when_creating_expense(): void
    {
        $user = User::factory()->create();
        $category = $this->category->id;

        $expenseData = [
            'amount' => 50.00,
            'category_id' => $category
        ];
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('api/expenses', $expenseData);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('title');
        $response->assertJsonValidationErrorFor('title');
    }

    public function test_amount_must_be_numeric(): void
    {
        $user = User::factory()->create();
        $category = $this->category->id;

        $expenseData = [
            'title' => 'Gasto no numerico',
            'amount' => 'No es un numero',
            'category_id' => $category
        ];
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('api/expenses', $expenseData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('amount');
    }

    /**
     * Test que valida múltiples casos de error.
     * 
     * @dataProvider invalidExpenseDataProvider
     */
    public function test_expense_validation_fails(array $invalidData, string $errorField): void
    {
        $user = User::factory()->create();
        $category = $this->category->id;

        if (isset($invalidData['category_id']) && $invalidData['category_id'] === 'valid'){
            $invalidData['category_id'] = $category;
        }

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/expenses', $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($errorField);
    }

    /**
     * Proveedor de datos inválidos para expenses.
     * 
     * Cada elemento del array es un CASO DE PRUEBA.
     */
    public static function invalidExpenseDataProvider(): array
    {
        return [
            // [datos a enviar, campo que debe fallar]

            'missing title' => [
                ['amount' => 50, 'category_id' =>'valid'],
                'title'
            ],

            'missing amount' => [
                ['title' => 'Test', 'category_id' => 'valid'],
                'amount'
            ],

            'missing category_id' => [
                ['title' => 'Test', 'amount' => 50],
                'category_id'
            ],

            'amount is string' => [
                ['title' => 'Test', 'amount' => 'not-a-number', 'category_id' => 'valid'],
                'amount'
            ],

            'amount is negative' => [
                ['title' => 'Test', 'amount' => -50, 'category_id' => 'valid'],
                'amount'
            ],

            'amount is zero' => [
                ['title' => 'Test', 'amount' => 0, 'category_id' => 'valid'],
                'amount'
            ],

            'category does not exist' => [
                ['title' => 'Test', 'amount' => 50, 'category_id' => 99999],
                'category_id'
            ],

            'title is too long' => [
                ['title' => str_repeat('a', 256), 'amount' => 50, 'category_id' => 'valid'],
                'title'
            ],
        ];
    }
}
