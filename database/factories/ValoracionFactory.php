<?php

namespace Database\Factories;

use App\Models\Juego;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Valoracion>
 */
class ValoracionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'juego_id' => Juego::factory(),
            'puntuacion' => fake()->numberBetween(1, 5),
            'comentario' => fake()->sentence(10),
        ];
    }
}
