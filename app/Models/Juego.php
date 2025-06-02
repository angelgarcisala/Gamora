<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Juego extends Model
{
    /** @use HasFactory<\Database\Factories\JuegoFactory> */
    use HasFactory;

    protected $fillable = ['titulo', 'descripcion', 'fecha_lanzamiento', 'desarrollador', 'editor', 'precio', 'url_descarga', 'nombre_ejecutable'];

    public function bibliotecas(): BelongsToMany {
        return $this->belongsToMany(Biblioteca::class);
    }

    public function multimedias(): HasMany {
        return $this->hasMany(Multimedia::class);
    }

    public function listasDeseados(): BelongsToMany {
        return $this->belongsToMany(ListaDeseados::class);
    }

    public function valoraciones(): HasMany {
        return $this->hasMany(Valoracion::class);
    }

    public function anuncios(): HasMany {
        return $this->hasMany(Anuncio::class);
    }
}
