<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tenant;

class TenantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->isSuperAdmin() || 
               ($user->tenant_id === $tenant->id && $user->isTenantAdmin());
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->isSuperAdmin() || 
               ($user->tenant_id === $tenant->id && $user->isTenantAdmin());
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->isSuperAdmin();
    }

    public function suspend(User $user, Tenant $tenant): bool
    {
        return $user->isSuperAdmin();
    }

    public function manageSettings(User $user, Tenant $tenant): bool
    {
        return $user->isSuperAdmin() || 
               ($user->tenant_id === $tenant->id && $user->isTenantAdmin());
    }
}
