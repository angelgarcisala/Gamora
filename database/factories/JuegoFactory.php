<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Juego>
 */
class JuegoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    // Aquí defino una forma más aleatoria de crear nombres simples de juegos, haciendo que puedan haber distintas combinaciones
    $adjetivos = [
        'Sombrío', 'Radiante', 'Feroz', 'Místico', 'Implacable', 'Lúgubre', 'Épico', 'Enigmático', 'Veloz', 'Antiguo'
    ];

    $sustantivos = [
        'Guerrero', 'Dragón', 'Titán', 'Hechicero', 'Asesino', 'Forjador', 'Bestia', 'Cazador', 'Nómada', 'Rey'
    ];

    $tematicas = [
        'del Inframundo', 'de las Sombras', 'del Futuro', 'de los Dioses', 'de Acero', 'de Fuego', 'del Abismo', 'del Espacio', 'del Caos', 'de Cristal'
    ];

    return [
        'titulo' => fake()->randomElement($adjetivos) . ' ' .
            fake()->randomElement($sustantivos) . ' ' .
            fake()->randomElement($tematicas) . ' ' .
            fake()->randomElement(['I', 'II', 'III', 'IV', '2077', 'Redux', 'Remastered']),

        'descripcion' => fake()->paragraph(4),
        'fecha_lanzamiento' => fake()->dateTimeBetween('-10 years', 'now'),
        'desarrollador' => fake()->company(), // company se invnenta nombres de compañías
        'editor' => fake()->company()
    ];
}

}
