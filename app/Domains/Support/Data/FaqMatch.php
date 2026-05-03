<?php

namespace App\Domains\Support\Data;

use App\Domains\Support\Models\SupportFaq;

class FaqMatch
{
    /**
     * @param array<int, string> $matchedKeywords
     */
    public function __construct(
        public readonly SupportFaq $faq,
        public readonly float $confidence,
        public readonly array $matchedKeywords,
    ) {
    }
}
