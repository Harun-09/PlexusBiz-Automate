<?php

namespace App\Policies;

use App\Domains\Social\Models\SocialPost;
use App\Models\User;

class SocialPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }

    public function view(User $user, SocialPost $socialPost): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }
}
