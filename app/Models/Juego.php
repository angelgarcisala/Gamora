<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Juego extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_lanzamiento',
        'desarrollador',
        'editor',
        'precio',
        'url_descarga',
        'nombre_ejecutable',
    ];

    /**
     * Bibliotecas que contienen este juego (muchos a muchos).
     */
    public function bibliotecas(): BelongsToMany
    {
        return $this->belongsToMany(Biblioteca::class);
    }

    /**
     * Multimedia asociada al juego.
     */
    public function multimedias(): HasMany
    {
        return $this->hasMany(Multimedia::class);
    }

    /**
     * Anuncios relacionados con este juego.
     */
    public function anuncios(): HasMany
    {
        return $this->hasMany(Anuncio::class);
    }

    /**
     * Reembolsa el precio de este juego a todos los compradores
     * y desvincula las bibliotecas asociadas.
     *
     * @return void
     */
    public function refundToBuyers(): void
    {
        // Usamos una transacción para asegurar consistencia
        DB::transaction(function () {
            // Recorrer cada biblioteca que contenga el juego
            foreach ($this->bibliotecas as $biblioteca) {
                $user = $biblioteca->user;
                // Incrementar el saldo del usuario por el precio del juego
                $user->increment('sueldo', $this->precio);
            }

            // Desvincular todas las bibliotecas (pivot)
            $this->bibliotecas()->detach();
        });
    }

    /**
     * Determina si un usuario dado es el desarrollador de este juego.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isOwnedBy($user): bool
    {
        return $this->desarrollador === $user->name;
    }
}
