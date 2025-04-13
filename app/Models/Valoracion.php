<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Valoracion extends Model
{
    /** @use HasFactory<\Database\Factories\ValoracionFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'juego_id', 'puntuacion', 'comentario'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    public function juego(): BelongsTo {
        return $this->belongsTo(Juego::class);
    }
}
