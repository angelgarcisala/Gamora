<?php
// database/seeders/MultimediaSeeder.php

namespace Database\Seeders;

use App\Models\Juego;
use Illuminate\Database\Seeder;

class MultimediaSeeder extends Seeder
{
    public function run(): void
    {
        Juego::all()->each(function ($juego) {
            // 1–2 vídeos
            \App\Models\Multimedia::factory()
                ->count(rand(1,2))
                ->create([
                    'juego_id' => $juego->id,
                    'tipo'     => 'video',
                ]);

            // 1–5 imágenes
            \App\Models\Multimedia::factory()
                ->count(rand(1,5))
                ->create([
                    'juego_id' => $juego->id,
                    'tipo'     => 'imagen',
                ]);
        });
    }
}
