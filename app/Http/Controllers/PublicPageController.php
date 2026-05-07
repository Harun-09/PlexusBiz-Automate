<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function about(): Response
    {
        return $this->render(
            title: 'About PlexusBiz',
            lead: 'PlexusBiz Automate connects B2B buyers, suppliers, and operations teams in one commerce platform.',
            sections: [
                [
                    'title' => 'What we do',
                    'content' => 'We combine marketplace discovery, supplier onboarding, RFQ, checkout, invoicing, and support so teams can run daily B2B operations from one product.',
                ],
                [
                    'title' => 'Who it helps',
                    'content' => 'Buyers can source and order products faster. Suppliers can manage catalogs and fulfillment. Admin teams can control access, modules, and automation.',
                ],
                [
                    'title' => 'How we operate',
                    'content' => 'Module-based routing and role-based access keep business workflows separated while still sharing one consistent data model.',
                ],
            ],
        );
    }

    public function contact(): Response
    {
        return $this->render(
            title: 'Contact',
            lead: 'Reach the PlexusBiz operations team for product, supplier, billing, and support issues.',
            sections: [
                [
                    'title' => 'Support',
                    'content' => 'Email: support@plexusbiz.com. For authenticated users, ticket workflows are available from the Support workspace.',
                ],
                [
                    'title' => 'Business office',
                    'content' => 'Dhaka, Bangladesh. Response times depend on request priority and current ticket volume.',
                ],
                [
                    'title' => 'Sales and onboarding',
                    'content' => 'For supplier onboarding or account setup, use the supplier application flow from the landing page.',
                ],
            ],
        );
    }

    public function terms(): Response
    {
        return $this->render(
            title: 'Terms',
            lead: 'These terms define platform usage responsibilities for buyers, suppliers, and administrators.',
            sections: [
                [
                    'title' => 'Account responsibility',
                    'content' => 'Users are responsible for account security, accurate profile information, and actions executed under their credentials.',
                ],
                [
                    'title' => 'Marketplace usage',
                    'content' => 'Product listings, quotes, and orders must be lawful and accurate. Misuse, fraud, or abuse may lead to account suspension.',
                ],
                [
                    'title' => 'Operational policies',
                    'content' => 'Ticket status updates, payment handling, and fulfillment workflows should follow the platform process and documented business rules.',
                ],
            ],
        );
    }

    public function privacy(): Response
    {
        return $this->render(
            title: 'Privacy',
            lead: 'This notice explains how PlexusBiz processes account and transaction data.',
            sections: [
                [
                    'title' => 'Data we process',
                    'content' => 'Profile details, order records, ticket messages, and workflow logs are processed to run platform operations.',
                ],
                [
                    'title' => 'Why we process data',
                    'content' => 'Data is used for account access, order fulfillment, invoicing, support, fraud prevention, and service reliability.',
                ],
                [
                    'title' => 'Data protection',
                    'content' => 'Role-based access controls, authenticated routes, and audit trails are used to reduce unauthorized access risk.',
                ],
            ],
        );
    }

    public function faq(): Response
    {
        return $this->render(
            title: 'FAQ',
            lead: 'Common questions about ordering, supplier onboarding, payments, and support.',
            sections: [
                [
                    'title' => 'How do I place a bulk order?',
                    'content' => 'Browse products, choose quantity, and proceed through cart and checkout. MOQ and pricing tiers are shown per product.',
                ],
                [
                    'title' => 'How can a supplier join?',
                    'content' => 'Use the supplier apply flow. Admin approval is required before supplier-only product actions become active.',
                ],
                [
                    'title' => 'Where can I get support?',
                    'content' => 'Authenticated users can create support tickets from the Support workspace. Public users can contact support by email.',
                ],
            ],
        );
    }

    /**
     * @param array<int, array{title: string, content: string}> $sections
     */
    private function render(string $title, string $lead, array $sections): Response
    {
        return Inertia::render('Public/StaticPage', [
            'title' => $title,
            'lead' => $lead,
            'sections' => $sections,
        ]);
    }
}
