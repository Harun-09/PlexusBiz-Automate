<?php

namespace App\Domains\Workflow\Services;

class ConditionEvaluator
{
    /**
     * @param array<int, array<string, mixed>>|null $conditions
     * @param array<string, mixed> $payload
     */
    public function passes(?array $conditions, array $payload): bool
    {
        if (blank($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            if (! $this->passesCondition($condition, $payload)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $condition
     * @param array<string, mixed> $payload
     */
    private function passesCondition(array $condition, array $payload): bool
    {
        $field = (string) ($condition['field'] ?? '');
        $operator = (string) ($condition['operator'] ?? 'equals');
        $expected = $condition['value'] ?? null;
        $actual = data_get($payload, $field);

        return match ($operator) {
            'equals' => $actual == $expected,
            'not_equals' => $actual != $expected,
            'greater_than' => (float) $actual > (float) $expected,
            'greater_than_or_equal' => (float) $actual >= (float) $expected,
            'less_than' => (float) $actual < (float) $expected,
            'contains' => is_array($actual) ? in_array($expected, $actual, true) : str_contains((string) $actual, (string) $expected),
            'in' => in_array($actual, (array) $expected, true),
            'truthy' => (bool) $actual,
            default => false,
        };
    }
}
