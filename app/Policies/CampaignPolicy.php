<?php

namespace App\Policies;

use App\Domains\Marketing\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }

    public function view(User $user, Campaign $campaign): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }
}
