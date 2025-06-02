<?php

namespace App\Policies;

use App\Models\Juego;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JuegoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Juego $juego): bool
    {
        return false;
    }

    public function comprar(User $user, Juego $juego): bool
    {
        if (! $user->biblioteca) {
            return true;
        }
        return ! $user->biblioteca->juegos->contains($juego->id);
    }

    public function jugar(User $user, Juego $juego): bool
    {
        return $user->biblioteca
            && $user->biblioteca->juegos->contains($juego->id);
    }

    public function valorar(User $user, Juego $juego): bool
    {
        return $user->biblioteca
            && $user->biblioteca->juegos->contains($juego->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Juego $juego): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Juego $juego): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Juego $juego): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Juego $juego): bool
    {
        return false;
    }
}
