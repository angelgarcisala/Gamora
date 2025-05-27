<?php

namespace App\Livewire;

use App\Models\Juego;
use Livewire\Component;

class BotonDescargaJuego extends Component
{
    public Juego $juego;
    public function render()
    {
        return view('livewire.boton-descarga-juego');
    }
}
