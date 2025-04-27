<?php

namespace Database\Seeders;

use App\Models\Juego;
use App\Models\User;
use App\Models\Valoracion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ValoracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $users = User::all();
        $juegos = Juego::all();

        foreach ($users as $user) {
            // Cada usuario valora de 1 a 3 juegos aleatorios
            foreach ($juegos->random(random_int(1, 3)) as $juego) {
                Valoracion::factory()->create([
                    'user_id' => $user->id,
                    'juego_id' => $juego->id,
                ]);
            }
        }
    }
}
