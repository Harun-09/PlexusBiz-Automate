<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Domains\Social\Models\SocialPost;
use App\Models\User;

class SocialPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::ManageSocialPosts->value);
    }

    public function view(User $user, SocialPost $socialPost): bool
    {
        return $user->hasPermissionTo(PermissionName::ManageSocialPosts->value);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::ManageSocialPosts->value);
    }

    public function update(User $user, SocialPost $socialPost): bool
    {
        return $user->hasPermissionTo(PermissionName::ManageSocialPosts->value);
    }

    public function delete(User $user, SocialPost $socialPost): bool
    {
        return $user->hasPermissionTo(PermissionName::ManageSocialPosts->value);
    }
}
