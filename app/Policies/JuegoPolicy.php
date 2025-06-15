<?php

namespace App\Policies;

use App\Models\Juego;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JuegoPolicy
{
    /**
     * Determine whether the user can view any modelos.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the modelo.
     */
    public function view(User $user, Juego $juego): bool
    {
        return false;
    }

    /**
     * Determine whether the user can comprar (buy) the modelo.
     */
    public function comprar(User $user, Juego $juego): bool
    {
        if (! $user->biblioteca) {
            return true;
        }
        return ! $user->biblioteca->juegos->contains($juego->id);
    }

    /**
     * Determine whether the user can jugar (play) the modelo.
     */
    public function jugar(User $user, Juego $juego): bool
    {
        return $user->biblioteca
            && $user->biblioteca->juegos->contains($juego->id);
    }

    /**
     * Determine whether the user can create modelos.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the modelo.
     */
    public function update(User $user, Juego $juego): bool
    {
        return $juego->desarrollador === $user->name;
    }

    /**
     * Determine whether the user can delete the modelo.
     * Ahora habilitado para el desarrollador del juego.
     */
    public function delete(User $user, Juego $juego): bool
    {
        return $juego->desarrollador === $user->name;
    }

    /**
     * Determine whether the user can restore the modelo.
     */
    public function restore(User $user, Juego $juego): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the modelo.
     */
    public function forceDelete(User $user, Juego $juego): bool
    {
        return false;
    }
}
