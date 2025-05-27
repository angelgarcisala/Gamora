<?php
// database/factories/MultimediaFactory.php

namespace Database\Factories;

use App\Models\Juego;
use App\Models\Multimedia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Mmo\Faker\PicsumProvider;

class MultimediaFactory extends Factory
{
    protected $model = Multimedia::class;

    public function definition(): array
    {
        return [
            // Se sobreescribe con create([ 'juego_id'=>..., 'tipo'=>'video' ]) en el seeder
            'juego_id' => Juego::inRandomOrder()->first()->id,
            'tipo'     => 'imagen',  // valor por defecto si no lo pasas
            'url'      => '',        // lo rellenamos justo después
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Multimedia $media) {
            // Nos aseguramos de tener el proveedor Picsum cargado
            fake()->addProvider(new PicsumProvider(fake()));

            if ($media->tipo === 'video') {
                // ->create(['tipo'=>'video', ...]) o default
                $videoUrls = json_decode(
                    File::get(base_path('database/data/videos_urls.json')),
                    true
                );
                $sourceUrl = fake()->randomElement($videoUrls);
                $filename  = Str::random(12) . '.mp4';
                $dest      = public_path("storage/videos/{$filename}");
                file_put_contents($dest, file_get_contents($sourceUrl));

                $media->url = "storage/videos/{$filename}";
            } else {
                // ->create(['tipo'=>'imagen', ...]) o default
                $filename  = fake()->picsum(
                    public_path('storage/images'),
                    640,
                    360,
                    false
                );
                $media->url = "storage/images/{$filename}";
            }
        });
    }
}
