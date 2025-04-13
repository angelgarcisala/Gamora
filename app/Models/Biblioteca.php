<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Biblioteca extends Model
{
    /** @use HasFactory<\Database\Factories\BibliotecaFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'fecha_adquisicion'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function juegos(): BelongsToMany {
        return $this->belongsToMany(Juego::class);
    }
}
