<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // عرض قائمة المستخدمين
    public function viewAny(User $user): bool
    {
        return $user->isOrganizer();  // يسمح فقط للمنظّم
    }

    // عرض مستخدم معيّن
    public function view(User $user, User $model): bool
    {
        return $user->isOrganizer();
    }

    // تحديث مستخدم
    public function update(User $user, User $model): bool
    {
        return $user->isOrganizer();
    }

    // حذف مستخدم
    public function delete(User $user, User $model): bool
    {
        return $user->isOrganizer();
    }

    // (اختياري) إنشاء مستخدم – حالياً route مستبعدة من store
    public function create(User $user): bool
    {
        return $user->isOrganizer();
    }
}
