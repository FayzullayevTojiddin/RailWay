<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Faqat super admin User modeliga kirishi mumkin
     */
    public function before(User $user, string $ability): bool|null
    {
        // Agar super bo'lsa, hamma huquq beriladi
        if ($user->role === 'super') {
            return true;
        }

        // Manager va viewer uchun User modeliga umuman ruxsat yo'q
        if (in_array($user->role, ['manager', 'viewer'])) {
            return false;
        }

        return null;
    }

    /**
     * Ko'rish huquqi
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'super';
    }

    /**
     * Bitta userni ko'rish
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === 'super';
    }

    /**
     * Yaratish huquqi
     */
    public function create(User $user): bool
    {
        return $user->role === 'super';
    }

    /**
     * O'zgartirish huquqi
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === 'super';
    }

    /**
     * O'chirish huquqi
     */
    public function delete(User $user, User $model): bool
    {
        return $user->role === 'super';
    }

    /**
     * Qayta tiklash huquqi
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === 'super';
    }

    /**
     * Butunlay o'chirish huquqi
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'super';
    }
}