<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RbacSeeder::class);
        $this->call(ECommerceSeeder::class);
        $this->call(CRMSeeder::class);
        $this->call(MarketingSeeder::class);
        $this->call(SocialSeeder::class);
        $this->call(SupportSeeder::class);
        $this->call(WorkflowSeeder::class);
        $this->call(EnhancedDemoSeeder::class);
        $this->call(BulkProductSeeder::class);
    }
}
