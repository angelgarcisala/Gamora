<?php

namespace Database\Factories;

use App\Models\Juego;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Anuncio>
 */
class AnuncioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Esto guardará un juego random
        $juego = Juego::inRandomOrder()->first();
    
        return [
            'juego_id' => $juego->id, //con esto asociará el id del juego random
            'titulo' => $juego->titulo . ' - ' . fake()->randomElement([
                '¡Ya disponible!',
                '¡Oferta limitada!',
                '¡Reserva ya!',
                'Descuento del 50%',
                'Solo por tiempo limitado',
            ]), // Y con todo esto, cogerá el título del juego, para poder añadirle algo promicional para el anuncio del juego
            'descripcion' => fake()->paragraph(),
            'fecha_inicio' => fake()->dateTimeBetween('-1 week', 'now'),
            'fecha_fin' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
