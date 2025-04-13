<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ListaDeseados extends Model
{
    /** @use HasFactory<\Database\Factories\ListaDeseadosFactory> */
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function juegos(): BelongsToMany{
        return $this->belongsToMany(Juego::class);
    }
}
