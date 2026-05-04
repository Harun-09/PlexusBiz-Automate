<?php

namespace App\Domains\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ModuleSetting extends Model
{
    protected $fillable = [
        'module_key',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static function enabledMap(): array
    {
        try {
            if (! static::getConnectionResolver()) {
                return [];
            }

            if (! Schema::hasTable((new static())->getTable())) {
                return [];
            }
        } catch (Throwable) {
            return [];
        }

        return static::query()
            ->pluck('enabled', 'module_key')
            ->map(fn (mixed $enabled): bool => (bool) $enabled)
            ->all();
    }

    public static function setEnabled(string $moduleKey, bool $enabled): self
    {
        return static::query()->updateOrCreate(
            ['module_key' => $moduleKey],
            ['enabled' => $enabled],
        );
    }
}
