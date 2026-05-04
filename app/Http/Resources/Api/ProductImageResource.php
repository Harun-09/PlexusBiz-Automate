<?php

namespace App\Http\Resources\Api;

use App\Domains\ECommerce\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProductImage */
class ProductImageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'path' => $this->publicPath(),
            'url' => $this->url(),
            'original_path' => $this->originalPath(),
            'public_path' => $this->publicPath(),
            'thumbnail_path' => $this->thumbnailPath(),
            'preview_path' => $this->previewPath(),
            'variants' => [
                'thumbnail' => $this->thumbnailPath() ? [
                    'path' => $this->thumbnailPath(),
                    'url' => $this->thumbnailUrl(),
                    'generated' => (bool) data_get($this->storageMeta(), 'variants.thumbnail.generated', false),
                    'max_width' => data_get($this->storageMeta(), 'variants.thumbnail.max_width'),
                    'max_height' => data_get($this->storageMeta(), 'variants.thumbnail.max_height'),
                ] : null,
                'preview' => $this->previewPath() ? [
                    'path' => $this->previewPath(),
                    'url' => $this->previewUrl(),
                    'generated' => (bool) data_get($this->storageMeta(), 'variants.preview.generated', false),
                    'max_width' => data_get($this->storageMeta(), 'variants.preview.max_width'),
                    'max_height' => data_get($this->storageMeta(), 'variants.preview.max_height'),
                ] : null,
            ],
            'storage_meta' => $this->storageMeta(),
            'alt_text' => $this->alt_text,
            'sort_order' => $this->sort_order,
            'is_primary' => $this->is_primary,
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
