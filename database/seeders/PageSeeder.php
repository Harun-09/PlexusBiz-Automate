<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Help Center',
                'slug' => 'help-center',
                'content' => '<p>Welcome to our Help Center. Here you will find answers to the most common questions.</p>',
                'meta_title' => 'Help Center - Knowledge Base',
                'meta_description' => 'Find answers to all your questions in our comprehensive Help Center.',
            ],
            [
                'title' => 'Track an Order',
                'slug' => 'track-an-order',
                'content' => '<p>Track your recent orders here. Please enter your order number and email address to see the current status.</p>',
                'meta_title' => 'Track Your Order',
                'meta_description' => 'Check the status and track the shipment of your recent orders.',
            ],
            [
                'title' => 'Return an Item',
                'slug' => 'return-an-item',
                'content' => '<p>If you are not satisfied with your purchase, you can return it within 30 days. Read our policy below for details.</p>',
                'meta_title' => 'Return an Item',
                'meta_description' => 'Information on how to return an item you purchased from us.',
            ],
            [
                'title' => 'Return Policy',
                'slug' => 'return-policy',
                'content' => '<p>Our return policy lasts 30 days. If 30 days have gone by since your purchase, unfortunately, we can’t offer you a refund or exchange.</p>',
                'meta_title' => 'Return Policy',
                'meta_description' => 'Our 30-day return and refund policy.',
            ],
            [
                'title' => 'Privacy & Security',
                'slug' => 'privacy-security',
                'content' => '<p>We take your privacy and security seriously. Here is how we protect your data.</p>',
                'meta_title' => 'Privacy & Security Policy',
                'meta_description' => 'Learn how we secure your data and protect your privacy.',
            ],
            [
                'title' => 'Feedback',
                'slug' => 'feedback',
                'content' => '<p>We love hearing from our customers! Please leave your feedback using the form below.</p>',
                'meta_title' => 'Customer Feedback',
                'meta_description' => 'Leave us feedback so we can improve our services.',
            ],
            [
                'title' => 'My Account',
                'slug' => 'my-account',
                'content' => '<p>Manage your account details, shipping addresses, and payment methods here.</p>',
                'meta_title' => 'My Account',
                'meta_description' => 'Manage your customer account and preferences.',
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
