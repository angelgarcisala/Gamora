<?php

namespace Database\Seeders;

use App\Models\Anuncio;
use App\Models\Juego;
use App\Models\Multimedia;
use App\Models\User;
use Database\Factories\JuegoFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        Juego::factory(30)->create();
        Anuncio::factory(60)->create();

        $this->call(MultimediaSeeder::class);
    }
}
