<?php

namespace App\Policies;

use App\Domains\Marketing\Models\CampaignTemplate;
use App\Models\User;

class CampaignTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }

    public function view(User $user, CampaignTemplate $campaignTemplate): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }

    public function update(User $user, CampaignTemplate $campaignTemplate): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }

    public function delete(User $user, CampaignTemplate $campaignTemplate): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }
}
