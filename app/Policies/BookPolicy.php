<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isTenantAdmin() || $user->isTeacher() || $user->isStudent();
    }

    public function view(User $user, Book $book): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->tenant_id === $book->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->isTenantAdmin();
    }

    public function update(User $user, Book $book): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $book->tenant_id;
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $book->tenant_id;
    }
}
