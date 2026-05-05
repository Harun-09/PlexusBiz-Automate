<?php

namespace App\Domains\CRM\Services;

use App\Domains\CRM\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

class CustomerSegmentationService
{
    /**
     * @param array<string, mixed> $filters
     * @return Builder<Customer>
     */
    public function query(array $filters): Builder
    {
        $query = Customer::buyerAccounts()->withCount('orders')->withSum('orders', 'grand_total');

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($stage = $filters['lifecycle_stage'] ?? null) {
            $query->where('lifecycle_stage', $stage);
        }

        if ($tags = $filters['tags'] ?? null) {
            foreach ((array) $tags as $tag) {
                $query->where(function (Builder $query) use ($tag): void {
                    $query->whereJsonContains('tags', $tag)
                        ->orWhere('tags', 'like', '%"'.$tag.'"%')
                        ->orWhere('tags', 'like', '%'.$tag.'%');
                });
            }
        }

        if ($minPurchaseCount = $filters['min_purchase_count'] ?? null) {
            $query->has('orders', '>=', (int) $minPurchaseCount);
        }

        if ($minOrderValue = $filters['min_total_spent'] ?? null) {
            $query->whereRaw(
                '(select coalesce(sum(cast(orders.grand_total as decimal(12,2))), 0) from orders where orders.customer_id = customers.id and orders.deleted_at is null) >= cast(? as decimal(12,2))',
                [(string) $minOrderValue],
            );
        }

        if ($lastActivityBefore = $filters['last_activity_before'] ?? null) {
            $query->where('last_activity_at', '<=', $lastActivityBefore);
        }

        return $query;
    }
}
