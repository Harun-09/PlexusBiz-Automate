<?php

namespace App\Domains\ECommerce\Models;

use App\Domains\ECommerce\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'category_id',
        'sku',
        'name',
        'slug',
        'description',
        'tags',
        'base_price',
        'moq',
        'stock_quantity',
        'reserved_quantity',
        'status',
        'published_at',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'tags' => 'array',
        'published_at' => 'datetime',
        'status' => ProductStatus::class,
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function pricingTiers(): HasMany
    {
        return $this->hasMany(PricingTier::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    public function availableStock(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    public function lowStockThreshold(): int
    {
        return max(10, (int) $this->moq);
    }

    public function isLowStock(): bool
    {
        return $this->availableStock() <= $this->lowStockThreshold();
    }

    public function primaryImage(): ?ProductImage
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images->firstWhere('is_primary', true) ?? $images->first();
    }

    public function primaryImageUrl(): string
    {
        return $this->primaryImage()?->url() ?: asset('images/landing/deal-imac.jpg');
    }

    /**
     * @return array<int, array{id:int,url:string,alt:string,is_primary:bool}>
     */
    public function galleryImages(): array
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images->map(function (ProductImage $image): array {
            return [
                'id' => $image->id,
                'url' => $image->url() ?: asset('images/landing/deal-imac.jpg'),
                'alt' => $image->alt_text ?: $this->name,
                'is_primary' => (bool) $image->is_primary,
            ];
        })->values()->all();
    }
}
