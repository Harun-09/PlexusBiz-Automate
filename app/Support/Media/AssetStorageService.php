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
        $this->persistManagedFile($file, $manifest);

        return ProductImage::create([
            'product_id' => $product->id,
            'path' => $manifest['public_path'],
            'alt_text' => $attributes['alt_text'] ?? $file->getClientOriginalName(),
            'sort_order' => (int) ($attributes['sort_order'] ?? 0),
            'is_primary' => (bool) ($attributes['is_primary'] ?? false),
            'storage_meta' => $manifest,
        ]);
    }

    public function replaceProductImage(Product $product, UploadedFile $file, array $attributes = []): ProductImage
    {
        $image = $this->storeProductImage($product, $file, array_merge($attributes, [
            'is_primary' => true,
            'sort_order' => 0,
        ]));

        ProductImage::query()
            ->where('product_id', $product->id)
            ->where('id', '!=', $image->id)
            ->get()
            ->each(function (ProductImage $existing): void {
                $this->deleteProductImageFiles($existing);
                $existing->delete();
            });

        return $image;
    }

    public function attachSocialMediaFile(SocialPost $post, UploadedFile $file, array $attributes = []): SocialPost
    {
        $manifest = $this->buildSocialManifest($post, $file);
        $this->persistManagedFile($file, $manifest);

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

    public function deleteProductImage(ProductImage $image): void
    {
        $this->deleteProductImageFiles($image);
        $image->delete();
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
                'thumbnail' => [
                    'disk' => $publicDisk,
                    'path' => $thumbnailPath,
                    'url' => Storage::disk($publicDisk)->url($thumbnailPath),
                    'max_width' => (int) config('media.thumbnail_max_width', 360),
                    'max_height' => (int) config('media.thumbnail_max_height', 360),
                    'generated' => false,
                ],
                'preview' => [
                    'disk' => $publicDisk,
                    'path' => $previewPath,
                    'url' => Storage::disk($publicDisk)->url($previewPath),
                    'max_width' => (int) config('media.preview_max_width', 1280),
                    'max_height' => (int) config('media.preview_max_height', 720),
                    'generated' => false,
                ],
            ],
        ]);
    }

    private function persistManagedFile(UploadedFile $file, array &$manifest): void
    {
        $this->copyToDisk($file, $manifest['original_disk'], $manifest['original_path']);
        $this->copyToDisk($file, $manifest['public_disk'], $manifest['public_path']);

        foreach (['thumbnail', 'preview'] as $variant) {
            $variantMeta = $manifest['variants'][$variant] ?? null;

            if (! is_array($variantMeta)) {
                continue;
            }

            $manifest['variants'][$variant]['generated'] = $this->writeManagedVariant(
                $file,
                $variantMeta['disk'],
                $variantMeta['path'],
                (int) $variantMeta['max_width'],
                (int) $variantMeta['max_height'],
            );
        }
    }

    private function assetPath(string $directory, string $segment, string $stem, string $extension): string
    {
        return trim($directory, '/').'/'.trim($segment, '/').'/'.$stem.'.'.$extension;
    }

    private function copyToDisk(UploadedFile $file, string $disk, string $path): void
    {
        Storage::disk($disk)->putFileAs(dirname($path), $file, basename($path));
    }

    private function writeManagedVariant(UploadedFile $file, string $disk, string $path, int $maxWidth, int $maxHeight): bool
    {
        $mimeType = strtolower((string) $file->getMimeType());
        $sourcePath = $file->getRealPath() ?: $file->getPathname();

        if ($sourcePath === '' || $sourcePath === false || ! is_file($sourcePath)) {
            $this->copyToDisk($file, $disk, $path);

            return false;
        }

        if (! $this->isImageMimeType($mimeType)) {
            $this->copyToDisk($file, $disk, $path);

            return false;
        }

        $binary = $this->resizeImageBinary($sourcePath, $mimeType, $maxWidth, $maxHeight);

        if ($binary === null) {
            $this->copyToDisk($file, $disk, $path);

            return false;
        }

        Storage::disk($disk)->put($path, $binary);

        return true;
    }

    private function resizeImageBinary(string $sourcePath, string $mimeType, int $maxWidth, int $maxHeight): ?string
    {
        if ($maxWidth < 1 || $maxHeight < 1) {
            return null;
        }

        $size = @getimagesize($sourcePath);
        if (! is_array($size) || empty($size[0]) || empty($size[1])) {
            return null;
        }

        $sourceWidth = (int) $size[0];
        $sourceHeight = (int) $size[1];
        $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight, 1);
        $targetWidth = max(1, (int) round($sourceWidth * $ratio));
        $targetHeight = max(1, (int) round($sourceHeight * $ratio));

        $sourceImage = $this->createImageFromPath($sourcePath, $mimeType);
        if ($sourceImage === null) {
            return null;
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($targetImage === false) {
            imagedestroy($sourceImage);

            return null;
        }

        $usesAlpha = in_array($mimeType, ['image/png', 'image/webp', 'image/avif'], true);
        if ($usesAlpha) {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        } else {
            $white = imagecolorallocate($targetImage, 255, 255, 255);
            imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $white);
        }

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight,
        );

        $binary = $this->encodeImageBinary($targetImage, $mimeType);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $binary;
    }

    private function createImageFromPath(string $sourcePath, string $mimeType)
    {
        return match ($mimeType) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/gif' => @imagecreatefromgif($sourcePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null,
            'image/avif' => function_exists('imagecreatefromavif') ? @imagecreatefromavif($sourcePath) : null,
            default => null,
        };
    }

    private function encodeImageBinary($image, string $mimeType): ?string
    {
        ob_start();

        $success = match ($mimeType) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => imagejpeg($image, null, (int) config('media.image_quality', 86)),
            'image/png' => imagepng($image, null, 6),
            'image/gif' => imagegif($image),
            'image/webp' => function_exists('imagewebp') ? imagewebp($image, null, (int) config('media.image_quality', 86)) : false,
            'image/avif' => function_exists('imageavif') ? imageavif($image, null, (int) config('media.image_quality', 86)) : false,
            default => false,
        };

        $binary = ob_get_clean();

        if (! $success || $binary === false || $binary === '') {
            return null;
        }

        return $binary;
    }

    private function isImageMimeType(string $mimeType): bool
    {
        return in_array($mimeType, [
            'image/jpeg',
            'image/jpg',
            'image/pjpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/avif',
        ], true);
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

    private function deleteProductImageFiles(ProductImage $image): void
    {
        $pathsByDisk = [];
        $this->pushDiskPath($pathsByDisk, (string) data_get($image->storageMeta(), 'original_disk', config('media.original_disk', 'local')), $image->originalPath());
        $this->pushDiskPath($pathsByDisk, (string) data_get($image->storageMeta(), 'public_disk', config('media.public_disk', 'public')), $image->publicPath());

        foreach (['thumbnail', 'preview'] as $variant) {
            $variantMeta = $image->variantMeta($variant);

            if (! is_array($variantMeta)) {
                continue;
            }

            $this->pushDiskPath($pathsByDisk, (string) data_get($variantMeta, 'disk', config('media.public_disk', 'public')), $image->variantPath($variant));
        }

        foreach ($pathsByDisk as $disk => $paths) {
            if ($paths === []) {
                continue;
            }

            Storage::disk($disk)->delete(array_values(array_unique($paths)));
        }
    }

    /**
     * @param array<string, array<int, string>> $pathsByDisk
     */
    private function pushDiskPath(array &$pathsByDisk, string $disk, ?string $path): void
    {
        $path = trim((string) $path);

        if ($disk === '' || $path === '' || $this->isExternalUrl($path)) {
            return;
        }

        $pathsByDisk[$disk] ??= [];
        $pathsByDisk[$disk][] = $path;
    }
}
