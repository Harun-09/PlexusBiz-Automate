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

    public function variantMeta(string $variant): ?array
    {
        $variantMeta = data_get($this->storageMeta(), 'variants.'.$variant);

        if (is_array($variantMeta)) {
            return $variantMeta;
        }

        if (is_string($variantMeta) && $variantMeta !== '') {
            return ['path' => $variantMeta];
        }

        return null;
    }

    public function variantPath(string $variant): ?string
    {
        $variantPath = data_get($this->variantMeta($variant) ?? [], 'path');

        return is_string($variantPath) && $variantPath !== '' ? $variantPath : null;
    }

    public function variantUrl(string $variant): ?string
    {
        $path = $this->variantPath($variant);

        if ($path === null) {
            return null;
        }

        return Storage::disk(config('media.public_disk', 'public'))->url($path);
    }

    public function thumbnailPath(): ?string
    {
        return $this->variantPath('thumbnail');
    }

    public function previewPath(): ?string
    {
        return $this->variantPath('preview');
    }

    public function thumbnailUrl(): ?string
    {
        return $this->variantUrl('thumbnail');
    }

    public function previewUrl(): ?string
    {
        return $this->variantUrl('preview');
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
