<?php

namespace Database\Factories;

use App\Models\Juego;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Biblioteca>
 */
class BibliotecaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'juego_id' => Juego::all()->random()->id,
            'fecha_adquisicion' => fake()->dateTimeBetween('-5 years', 'now'),
        ];
    }
}
