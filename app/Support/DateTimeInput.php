<?php

namespace App\Support;

use Carbon\Carbon;

final class DateTimeInput
{
    public const DEFAULT_TIMEZONE = 'Asia/Dhaka';

    /**
     * Normalize a datetime-local or ISO timestamp into the app storage timezone.
     */
    public static function toDatabase(mixed $value, ?string $sourceTimezone = null): ?string
    {
        $raw = trim((string) $value);

        if ($raw === '') {
            return null;
        }

        $sourceTimezone ??= self::DEFAULT_TIMEZONE;

        return Carbon::parse($raw, $sourceTimezone)
            ->setTimezone(config('app.timezone', 'UTC'))
            ->toDateTimeString();
    }

    /**
     * Format a stored datetime for a datetime-local input.
     */
    public static function toInputValue(mixed $value, ?string $targetTimezone = null): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $targetTimezone ??= self::DEFAULT_TIMEZONE;
        $date = $value instanceof Carbon ? $value->copy() : Carbon::parse((string) $value);

        return $date
            ->setTimezone($targetTimezone)
            ->format('Y-m-d\TH:i');
    }
}
