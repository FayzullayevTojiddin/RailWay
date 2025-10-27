<?php

namespace App\Policies;

use App\Models\Cadastre;
use App\Models\User;

class CadastrePolicy
{
    /**
     * Super admin har doim ruxsat oladi
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'super') {
            return true;
        }

        return null;
    }

    /**
     * Ko'rish huquqi
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super', 'manager', 'viewer']);
    }

    /**
     * Bitta yozuvni ko'rish
     */
    public function view(User $user, Cadastre $cadastre): bool
    {
        return in_array($user->role, ['super', 'manager', 'viewer']);
    }

    /**
     * Yaratish huquqi
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super', 'manager']);
    }

    /**
     * O'zgartirish huquqi
     */
    public function update(User $user, Cadastre $cadastre): bool
    {
        return in_array($user->role, ['super', 'manager']);
    }

    /**
     * O'chirish huquqi
     */
    public function delete(User $user, Cadastre $cadastre): bool
    {
        return $user->role === 'super';
    }

    /**
     * Qayta tiklash huquqi
     */
    public function restore(User $user, Cadastre $cadastre): bool
    {
        return $user->role === 'super';
    }

    /**
     * Butunlay o'chirish huquqi
     */
    public function forceDelete(User $user, Cadastre $cadastre): bool
    {
        return $user->role === 'super';
    }
}