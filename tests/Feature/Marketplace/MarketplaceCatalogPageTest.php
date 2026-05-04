<?php

namespace Tests\Feature\Marketplace;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MarketplaceCatalogPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_orders_and_moq_pricing_open_distinct_entry_pages(): void
    {
        $this->get('/products/bulk-orders')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketplace/Products/Index')
                ->where('mode', 'bulk')
                ->where('filters.quick', 'bulk'));

        $this->get('/products/moq-pricing')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketplace/Products/Index')
                ->where('mode', 'moq')
                ->where('filters.quick', 'moq'));
    }

    public function test_quick_query_fallback_still_works_on_the_base_catalog_route(): void
    {
        $this->get('/products?quick=bulk')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketplace/Products/Index')
                ->where('mode', 'bulk')
                ->where('filters.quick', 'bulk'));

        $this->get('/products?quick=moq')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketplace/Products/Index')
                ->where('mode', 'moq')
                ->where('filters.quick', 'moq'));
    }
}
