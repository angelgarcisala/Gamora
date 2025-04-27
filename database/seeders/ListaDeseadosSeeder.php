<?php

namespace Database\Seeders;

use App\Models\Juego;
use App\Models\ListaDeseados;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListaDeseadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $juegoIds = Juego::pluck('id')->toArray();

        foreach (User::all() as $user) {
            $lista = ListaDeseados::factory()->create([
                'user_id' => $user->id,
            ]);

            shuffle($juegoIds);
            $lista->juegos()->attach(array_splice($juegoIds, 0, random_int(1, min(5, count($juegoIds)))));
        }
    }
}
