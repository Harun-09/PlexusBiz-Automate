<?php

namespace App\Http\Controllers;

use App\Domains\ECommerce\Models\Order;
use App\Domains\Social\Enums\SocialPostStatus;
use Illuminate\Http\RedirectResponse;

class RouteAliasController extends Controller
{
    public function authLogin(): RedirectResponse
    {
        return redirect()->route('login');
    }

    public function customerProfileAlias(): RedirectResponse
    {
        return redirect()->route('profile.edit');
    }

    public function buyerTicketsAlias(): RedirectResponse
    {
        return redirect()->route('support.tickets.index');
    }

    public function ordersIndexAlias(): RedirectResponse
    {
        return redirect()->route('commerce.orders.index');
    }

    public function adminLeadsIndexAlias(): RedirectResponse
    {
        return redirect()->route('crm.leads.index');
    }

    public function adminLeadsCreateAlias(): RedirectResponse
    {
        return redirect()->route('crm.leads.create');
    }

    public function adminCampaignsIndexAlias(): RedirectResponse
    {
        return redirect()->route('marketing.campaigns.index');
    }

    public function adminCampaignsCreateAlias(): RedirectResponse
    {
        return redirect()->route('marketing.campaigns.create');
    }

    public function adminTemplatesIndexAlias(): RedirectResponse
    {
        return redirect()->route('marketing.templates.index');
    }

    public function adminSocialPostsIndexAlias(): RedirectResponse
    {
        return redirect()->route('social.posts.index');
    }

    public function adminSocialPostsCreateAlias(): RedirectResponse
    {
        return redirect()->route('social.posts.create');
    }

    public function adminSocialCalendarAlias(): RedirectResponse
    {
        return redirect()->route('social.calendar');
    }

    public function adminAutomationRulesIndexAlias(): RedirectResponse
    {
        return redirect()->route('workflow.rules.index');
    }

    public function adminAutomationRulesCreateAlias(): RedirectResponse
    {
        return redirect()->route('workflow.rules.create');
    }

    public function adminWorkflowLogsAlias(): RedirectResponse
    {
        return redirect()->route('workflow.logs.index');
    }

    public function adminTicketsIndexAlias(): RedirectResponse
    {
        return redirect()->route('support.tickets.index');
    }

    public function marketingIndexAlias(): RedirectResponse
    {
        return redirect()->route('marketing.campaigns.index');
    }

    public function ordersShowAlias(Order $order): RedirectResponse
    {
        return redirect()->route('commerce.orders.index', ['search' => $order->order_number]);
    }

    public function supportIndex(): RedirectResponse
    {
        return redirect()->route('support.tickets.index');
    }

    public function socialIndex(): RedirectResponse
    {
        return redirect()->route('social.calendar');
    }

    public function socialCampaignsIndex(): RedirectResponse
    {
        return redirect()->route('social.posts.index');
    }

    public function socialScheduledPosts(): RedirectResponse
    {
        return redirect()->route('social.posts.index', [
            'status' => SocialPostStatus::Scheduled->value,
        ]);
    }

    public function workflowIndex(): RedirectResponse
    {
        return redirect()->route('workflow.rules.index');
    }
}
