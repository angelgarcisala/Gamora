<?php

namespace Database\Factories;

use App\Models\Juego;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MultimediaFactory extends Factory
{
    public function definition(): array
    {
        // Añadir el proveedor de Picsum directamente
        fake()->addProvider(new \Mmo\Faker\PicsumProvider(fake()));

        // Cargar el array de URLs del JSON
        $videoUrls = json_decode(File::get(base_path('database\data\videos_urls.json')), true);

        // Aleatoriamente decidir si es imagen o video
        $isVideo = fake()->boolean(30); // 30% probabilidad de ser video

        if ($isVideo) {
            // Seleccionar una URL aleatoria
            $url = fake()->randomElement($videoUrls);
            $filename = Str::random(12) . '.mp4';
            $storagePath = public_path("storage/videos/$filename");

            // Descargar el archivo
            file_put_contents($storagePath, file_get_contents($url));

            return [
                'tipo' => 'video',
                'url' => "storage/videos/$filename",
                'juego_id' => Juego::inRandomOrder()->first()->id,
            ];
        } else {
            // Generar imagen usando Picsum
            $filename = fake()->picsum(public_path('storage/images'), 640, 360, false);

            return [
                'tipo' => 'imagen',
                'url' => "storage/images/$filename",
                'juego_id' => Juego::inRandomOrder()->first()->id,
            ];
        }
    }
}
