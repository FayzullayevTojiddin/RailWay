<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    /**
     * Super admin bo‘lsa barcha ruxsatlar true bo‘ladi.
     */
    private function isSuper(User $user): bool
    {
        return $user->role === 'super';
    }

    /**
     * Ro‘yxatni ko‘rishga ruxsat.
     */
    public function viewAny(User $user): bool
    {
        return true; // hamma ko‘ra oladi
    }

    /**
     * Bitta Report-ni ko‘rish.
     */
    public function view(User $user, Report $report): bool
    {
        return true; // hamma ko‘ra oladi
    }

    /**
     * Report yaratish — faqat super.
     */
    public function create(User $user): bool
    {
        return $this->isSuper($user);
    }

    /**
     * Report taxrirlash — faqat super.
     */
    public function update(User $user, Report $report): bool
    {
        return $this->isSuper($user);
    }

    /**
     * Report o‘chirish — faqat super.
     */
    public function delete(User $user, Report $report): bool
    {
        return $this->isSuper($user);
    }
}