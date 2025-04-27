<?php

namespace Database\Seeders;

use App\Models\Biblioteca;
use App\Models\Juego;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BibliotecaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $juegoIds = Juego::pluck('id')->toArray();

        foreach (User::all() as $user) {
            $biblioteca = Biblioteca::factory()->create([
                'user_id' => $user->id,
            ]);

            // Añadir juegos aleatorios a la biblioteca (entre 1 y 5 juegos)
            shuffle($juegoIds);
            $biblioteca->juegos()->attach(array_slice($juegoIds, 0, random_int(1, min(5, count($juegoIds)))));
        }
    }
}
