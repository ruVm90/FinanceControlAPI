<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Comida',
            'Transporte', 
            'Entretenimiento',
            'Salud',
            'Educación',
            'Compras',
            'Servicios',
            'Otros',
        ]);
        
        return [
            'name' => $name,
            
        ];
    }
}
