<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Multimedia extends Model
{
    /** @use HasFactory<\Database\Factories\MultimediaFactory> */
    use HasFactory;

    protected $fillable = ['juego_id', 'tipo', 'url'];

    public function juego(): BelongsTo {
        return $this->belongsTo(Juego::class);
    }
}
