<?php

namespace App\Support\Media;

use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\ProductImage;
use App\Domains\Social\Models\SocialPost;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetStorageService
{
    public function storeProductImage(Product $product, UploadedFile $file, array $attributes = []): ProductImage
    {
        $manifest = $this->buildProductManifest($product, $file);
        $this->copyToDisk($file, $manifest['original_disk'], $manifest['original_path']);
        $this->copyToDisk($file, $manifest['public_disk'], $manifest['public_path']);

        return ProductImage::create([
            'product_id' => $product->id,
            'path' => $manifest['public_path'],
            'alt_text' => $attributes['alt_text'] ?? $file->getClientOriginalName(),
            'sort_order' => (int) ($attributes['sort_order'] ?? 0),
            'is_primary' => (bool) ($attributes['is_primary'] ?? false),
            'storage_meta' => $manifest,
        ]);
    }

    public function attachSocialMediaFile(SocialPost $post, UploadedFile $file, array $attributes = []): SocialPost
    {
        $manifest = $this->buildSocialManifest($post, $file);
        $this->copyToDisk($file, $manifest['original_disk'], $manifest['original_path']);
        $this->copyToDisk($file, $manifest['public_disk'], $manifest['public_path']);

        $post->forceFill(array_merge([
            'media_url' => $manifest['public_url'],
            'media_meta' => $manifest,
        ], $attributes))->save();

        return $post->refresh();
    }

    public function publicUrl(?string $path, ?string $disk = null): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if ($this->isExternalUrl($path)) {
            return $path;
        }

        return Storage::disk($disk ?: config('media.public_disk', 'public'))->url($path);
    }

    public function productVariantPath(Product $product, string $variant, string $extension): string
    {
        return $this->assetPath(
            $this->productDirectory($product),
            $variant,
            $this->productStem($product, $variant),
            $extension,
        );
    }

    public function socialVariantPath(SocialPost $post, string $variant, string $extension): string
    {
        return $this->assetPath(
            $this->socialDirectory($post),
            $variant,
            $this->socialStem($post, $variant),
            $extension,
        );
    }

    public function buildProductManifest(Product $product, UploadedFile $file): array
    {
        $extension = $this->normalizeExtension($file);
        $directory = $this->productDirectory($product);
        $stem = $this->productStem($product, pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        return $this->buildManifest(
            $directory,
            $stem,
            $extension,
            [
                'owner_type' => 'product',
                'owner_id' => $product->id,
                'owner_key' => $this->productOwnerKey($product),
                'alt_text' => $product->name,
            ],
            $file,
        );
    }

    public function buildSocialManifest(SocialPost $post, UploadedFile $file): array
    {
        $extension = $this->normalizeExtension($file);
        $directory = $this->socialDirectory($post);
        $stem = $this->socialStem($post, pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        return $this->buildManifest(
            $directory,
            $stem,
            $extension,
            [
                'owner_type' => 'social_post',
                'owner_id' => $post->id,
                'owner_key' => $this->socialOwnerKey($post),
                'platform' => $post->platform->value,
            ],
            $file,
        );
    }

    private function buildManifest(string $directory, string $stem, string $extension, array $context, UploadedFile $file): array
    {
        $originalDisk = config('media.original_disk', 'local');
        $publicDisk = config('media.public_disk', 'public');
        $originalPath = $this->assetPath($directory, config('media.original_segment', 'original'), $stem, $extension);
        $publicPath = $this->assetPath($directory, config('media.public_segment', 'public'), $stem, $extension);
        $thumbnailPath = $this->assetPath($directory, config('media.variants_segment', 'variants').'/thumbnail', $stem, $extension);
        $previewPath = $this->assetPath($directory, config('media.variants_segment', 'variants').'/preview', $stem, $extension);

        return array_merge($context, [
            'strategy' => 'private-original-public-copy',
            'original_disk' => $originalDisk,
            'original_path' => $originalPath,
            'public_disk' => $publicDisk,
            'public_path' => $publicPath,
            'public_url' => Storage::disk($publicDisk)->url($publicPath),
            'file_name' => basename($publicPath),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'source_name' => $file->getClientOriginalName(),
            'variants' => [
                'thumbnail' => $thumbnailPath,
                'preview' => $previewPath,
            ],
        ]);
    }

    private function assetPath(string $directory, string $segment, string $stem, string $extension): string
    {
        return trim($directory, '/').'/'.trim($segment, '/').'/'.$stem.'.'.$extension;
    }

    private function copyToDisk(UploadedFile $file, string $disk, string $path): void
    {
        Storage::disk($disk)->putFileAs(dirname($path), $file, basename($path));
    }

    private function normalizeExtension(UploadedFile $file): string
    {
        $extension = strtolower(trim((string) $file->getClientOriginalExtension()));

        if ($extension !== '') {
            return $extension;
        }

        $fallback = strtolower(trim((string) $file->extension()));

        return $fallback !== '' ? $fallback : 'bin';
    }

    private function productDirectory(Product $product): string
    {
        return trim(config('media.product_root', 'media/products'), '/').'/'.$this->productOwnerKey($product).'/product-'.$product->id;
    }

    private function socialDirectory(SocialPost $post): string
    {
        return trim(config('media.social_root', 'media/social'), '/').'/'.$this->socialOwnerKey($post).'/post-'.$post->id;
    }

    private function productOwnerKey(Product $product): string
    {
        $product->loadMissing('supplier');
        $supplierKey = $product->supplier?->slug ?: 'supplier-'.$product->supplier_id;

        return $this->slugSegment($supplierKey);
    }

    private function socialOwnerKey(SocialPost $post): string
    {
        return $this->slugSegment('platform-'.$post->platform->value);
    }

    private function productStem(Product $product, string $seed): string
    {
        return $this->buildStem('product-'.$product->id, $product->sku ?: $product->name, $seed);
    }

    private function socialStem(SocialPost $post, string $seed): string
    {
        return $this->buildStem('social-'.$post->id, $post->platform->value, $seed);
    }

    private function buildStem(string $prefix, string $owner, string $seed): string
    {
        $parts = [
            $this->slugSegment($prefix),
            $this->slugSegment($owner),
            $this->slugSegment($seed),
            Str::lower(Str::random(8)),
        ];

        return implode('-', array_values(array_filter($parts)));
    }

    private function slugSegment(string $value): string
    {
        $segment = Str::slug($value, '-');

        return $segment !== '' ? $segment : 'asset';
    }

    private function isExternalUrl(string $value): bool
    {
        return Str::startsWith($value, ['http://', 'https://', '//']);
    }
}
