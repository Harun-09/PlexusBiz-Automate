<?php

namespace Tests\Feature\Api;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialAccount;
use App\Domains\Social\Models\SocialPost;
use App\Models\User;
use App\Support\Media\AssetStorageService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MediaAssetStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_exposes_media_storage_paths_and_urls(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        Sanctum::actingAs(User::where('email', 'admin@plexus.test')->firstOrFail());

        $product = $this->product();
        $productImage = app(AssetStorageService::class)->storeProductImage(
            $product,
            UploadedFile::fake()->createWithContent('product-image.jpg', 'product-image-bytes'),
            [
                'alt_text' => 'Front angle',
                'sort_order' => 1,
                'is_primary' => true,
            ],
        );

        $this->getJson('/api/v1/products/'.$product->id)
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.images.0.id', $productImage->id)
            ->assertJsonPath('data.images.0.path', $productImage->publicPath())
            ->assertJsonPath('data.images.0.original_path', $productImage->originalPath())
            ->assertJsonPath('data.images.0.public_path', $productImage->publicPath())
            ->assertJsonPath('data.images.0.storage_meta.strategy', 'private-original-public-copy')
            ->assertJsonPath('data.images.0.url', $productImage->url())
            ->assertJsonPath('data.images.0.is_primary', true);

        Storage::disk('local')->assertExists($productImage->originalPath());
        Storage::disk('public')->assertExists($productImage->publicPath());

        $account = SocialAccount::create([
            'platform' => SocialPlatform::Facebook,
            'name' => 'PlexusBiz social account',
            'handle' => '@plexusbiz',
            'status' => 'active',
            'credentials_json' => ['mode' => 'mock'],
        ]);

        $socialPost = SocialPost::create([
            'campaign_id' => null,
            'social_account_id' => $account->id,
            'platform' => SocialPlatform::Facebook,
            'content' => 'Scheduled post with managed media storage',
            'scheduled_at' => now()->addHour(),
            'status' => SocialPostStatus::Scheduled,
        ]);

        $socialPost = app(AssetStorageService::class)->attachSocialMediaFile(
            $socialPost,
            UploadedFile::fake()->createWithContent('social-media.png', 'social-media-bytes'),
        );

        $this->getJson('/api/v1/social-posts/'.$socialPost->id)
            ->assertOk()
            ->assertJsonPath('data.id', $socialPost->id)
            ->assertJsonPath('data.media_url', $socialPost->mediaUrl())
            ->assertJsonPath('data.media.url', $socialPost->mediaUrl())
            ->assertJsonPath('data.media.original_path', $socialPost->mediaOriginalPath())
            ->assertJsonPath('data.media.public_path', $socialPost->mediaPublicPath())
            ->assertJsonPath('data.media.storage_meta.strategy', 'private-original-public-copy')
            ->assertJsonPath('data.media.is_external', false);

        Storage::disk('local')->assertExists($socialPost->mediaOriginalPath());
        Storage::disk('public')->assertExists($socialPost->mediaPublicPath());
    }

    private function product(): Product
    {
        $user = User::factory()->create();
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'company_name' => 'Storage Supplier',
            'slug' => 'storage-supplier-'.Str::random(12),
            'status' => SupplierStatus::Approved,
            'contact_email' => $user->email,
            'approved_at' => now(),
        ]);

        return Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => null,
            'sku' => 'PX-STORAGE-'.Str::upper(Str::random(8)),
            'name' => 'Storage Convention Product',
            'slug' => 'storage-convention-product-'.Str::random(12),
            'description' => 'Product used to verify media storage conventions.',
            'base_price' => '120.00',
            'moq' => 1,
            'stock_quantity' => 24,
            'reserved_quantity' => 0,
            'status' => ProductStatus::Active,
            'published_at' => now(),
        ]);
    }
}
