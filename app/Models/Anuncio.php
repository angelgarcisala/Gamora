<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anuncio extends Model
{
    /** @use HasFactory<\Database\Factories\AnuncioFactory> */
    use HasFactory;
    protected $fillable = ['juego_id', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin'];

    public function juego (): BelongsTo{
        return $this->belongsTo(Juego::class);
    }
}
