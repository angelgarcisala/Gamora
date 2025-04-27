<?php

namespace Database\Seeders;

use App\Models\Carrito;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarritoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
    foreach (User::all() as $user) {
        Carrito::factory()->create([
            'user_id' => $user->id,
        ]);
    }
    }
}
