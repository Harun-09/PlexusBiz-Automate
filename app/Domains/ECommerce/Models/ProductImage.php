<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'path',
        'storage_meta',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'storage_meta' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function storageMeta(): array
    {
        return is_array($this->storage_meta) ? $this->storage_meta : [];
    }

    public function originalPath(): ?string
    {
        $originalPath = data_get($this->storageMeta(), 'original_path');

        return $originalPath !== null && $originalPath !== '' ? $originalPath : null;
    }

    public function publicPath(): ?string
    {
        $publicPath = data_get($this->storageMeta(), 'public_path');

        if ($publicPath !== null && $publicPath !== '') {
            return $publicPath;
        }

        $path = trim((string) $this->getRawOriginal('path'));

        return $path !== '' ? $path : null;
    }

    public function url(): ?string
    {
        $path = $this->publicPath();

        if ($path === null) {
            return null;
        }

        if ($this->isExternalUrl($path)) {
            return $path;
        }

        return Storage::disk(config('media.public_disk', 'public'))->url($path);
    }

    private function isExternalUrl(string $value): bool
    {
        return Str::startsWith($value, ['http://', 'https://', '//']);
    }
}
