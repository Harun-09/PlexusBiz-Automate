<?php

namespace App\Domains\Support\Services;

use App\Domains\Support\Data\FaqMatch;
use App\Domains\Support\Enums\SupportFaqStatus;
use App\Domains\Support\Models\SupportFaq;

class FaqMatcher
{
    public function match(string $message): ?FaqMatch
    {
        $normalized = $this->normalize($message);
        $best = null;

        SupportFaq::query()
            ->where('status', SupportFaqStatus::Active->value)
            ->orderBy('priority')
            ->get()
            ->each(function (SupportFaq $faq) use ($normalized, &$best): void {
                $keywords = $this->keywordsFor($faq);
                $matched = array_values(array_filter(
                    $keywords,
                    fn (string $keyword): bool => $keyword !== '' && str_contains($normalized, $keyword),
                ));

                if ($matched === []) {
                    return;
                }

                $confidence = min(1.0, 0.45 + (count($matched) / max(1, count($keywords))));
                $candidate = new FaqMatch($faq, round($confidence, 2), $matched);

                if ($best === null || $candidate->confidence > $best->confidence) {
                    $best = $candidate;
                }
            });

        return $best;
    }

    /**
     * @return array<int, string>
     */
    private function keywordsFor(SupportFaq $faq): array
    {
        preg_match_all('/[a-z0-9]{4,}/', $this->normalize($faq->question), $terms);

        return collect($faq->keywords_json ?? [])
            ->map(fn (mixed $keyword): string => $this->normalize((string) $keyword))
            ->merge($terms[0] ?? [])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalize(string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', mb_strtolower($value)));
    }
}
