<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'moq' => $this->moq,
            'stock_quantity' => $this->stock_quantity,
            'reserved_quantity' => $this->reserved_quantity,
            'available_stock' => $this->availableStock(),
            'status' => $this->status->value,
            'supplier' => $this->whenLoaded('supplier', fn (): array => [
                'id' => $this->supplier?->id,
                'company_name' => $this->supplier?->company_name,
                'slug' => $this->supplier?->slug,
            ]),
            'category' => $this->whenLoaded('category', fn (): ?array => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null),
            'images' => $this->whenLoaded('images', fn (): array => ProductImageResource::collection($this->images)->resolve($request)),
            'primary_image_url' => $this->primaryImageUrl(),
            'gallery' => $this->whenLoaded('images', fn (): array => collect($this->galleryImages())->values()->all()),
            'pricing_tiers' => $this->whenLoaded('pricingTiers', fn (): array => $this->pricingTiers
                ->sortBy('min_quantity')
                ->values()
                ->map(fn ($tier): array => [
                    'id' => $tier->id,
                    'min_quantity' => $tier->min_quantity,
                    'unit_price' => $tier->unit_price,
                ])
                ->all()),
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
