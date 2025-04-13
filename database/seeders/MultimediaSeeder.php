<?php

namespace Database\Seeders;

use App\Models\Juego;
use App\Models\Multimedia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MultimediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Juego::all()->each(function ($juego) {
            // Videos
            Multimedia::factory()->count(rand(1, 2))->create(['juego_id' => $juego->id, 'tipo' => 'video']);
        
            // Imágenes
            Multimedia::factory()->count(rand(1, 5))->create(['juego_id' => $juego->id, 'tipo' => 'imagen']);
        });
    }
}
