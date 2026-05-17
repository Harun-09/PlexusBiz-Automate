<?php

namespace App\DTOs\ECommerce;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Product;

class ProductUpsertData
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(public readonly array $attributes)
    {
    }

    /**
     * @param array<string, mixed> $validated
     * @param array<int, string>|null $normalizedTags
     */
    public static function fromValidated(
        array $validated,
        int $supplierId,
        ?array $normalizedTags,
        string $slug,
        ?Product $existingProduct = null,
    ): self {
        $status = (string) ($validated['status'] ?? ($existingProduct?->status?->value ?? ProductStatus::Draft->value));
        $isActive = $status === ProductStatus::Active->value;

        $attributes = [
            'supplier_id' => $supplierId,
            'category_id' => $validated['category_id'] ?? $existingProduct?->category_id,
            'sku' => array_key_exists('sku', $validated) ? trim((string) $validated['sku']) : $existingProduct?->sku,
            'name' => array_key_exists('name', $validated) ? trim((string) $validated['name']) : $existingProduct?->name,
            'slug' => $slug,
            'description' => array_key_exists('description', $validated) ? ($validated['description'] ?: null) : $existingProduct?->description,
            'tags' => array_key_exists('tags', $validated) ? $normalizedTags : $existingProduct?->tags,
            'base_price' => $validated['base_price'] ?? $existingProduct?->base_price,
            'moq' => $validated['moq'] ?? $existingProduct?->moq,
            'stock_quantity' => $validated['stock_quantity'] ?? $existingProduct?->stock_quantity,
            'reserved_quantity' => $validated['reserved_quantity'] ?? $existingProduct?->reserved_quantity ?? 0,
            'status' => $status,
            'published_at' => $isActive
                ? ($validated['published_at'] ?? $existingProduct?->published_at ?? now())
                : null,
        ];

        return new self($attributes);
    }
}
