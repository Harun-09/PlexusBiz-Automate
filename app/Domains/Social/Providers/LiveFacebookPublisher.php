<?php

namespace App\Domains\Social\Providers;

use App\Domains\Social\Contracts\SocialPublisher;
use App\Domains\Social\Data\SocialPublishResult;
use App\Domains\Social\Models\SocialPost;
use Illuminate\Support\Facades\Http;
use Throwable;

class LiveFacebookPublisher implements SocialPublisher
{
    public function publish(SocialPost $post): SocialPublishResult
    {
        $credentials = is_array($post->account?->credentials_json) ? $post->account->credentials_json : [];
        $pageId = trim((string) data_get($credentials, 'page_id', ''));
        $accessToken = trim((string) data_get($credentials, 'access_token', ''));

        if ($pageId === '' || $accessToken === '') {
            return new SocialPublishResult(
                successful: false,
                provider: 'facebook_graph_api',
                error: 'Facebook Page ID and access token are required for live publishing.',
            );
        }

        $payload = [
            'message' => $post->content,
            'access_token' => $accessToken,
        ];

        $mediaUrl = method_exists($post, 'mediaUrl') ? trim((string) $post->mediaUrl()) : '';

        if ($mediaUrl !== '' && filter_var($mediaUrl, FILTER_VALIDATE_URL)) {
            $payload['link'] = $mediaUrl;
        }

        try {
            $response = Http::asForm()->post($this->endpoint($pageId), $payload);
            $body = $response->json();

            if (! $response->successful()) {
                return new SocialPublishResult(
                    successful: false,
                    provider: 'facebook_graph_api',
                    response: is_array($body) ? $body : [],
                    error: $this->errorMessage($body, 'Facebook Graph API request failed.'),
                );
            }

            return new SocialPublishResult(
                successful: true,
                provider: 'facebook_graph_api',
                externalPostId: (string) (data_get($body, 'id') ?? data_get($body, 'post_id') ?? ''),
                engagement: [
                    'likes_count' => 0,
                    'comments_count' => 0,
                    'shares_count' => 0,
                    'reach_count' => 0,
                    'clicks_count' => 0,
                ],
                response: is_array($body) ? $body : [],
            );
        } catch (Throwable $exception) {
            return new SocialPublishResult(
                successful: false,
                provider: 'facebook_graph_api',
                error: $exception->getMessage(),
            );
        }
    }

    private function endpoint(string $pageId): string
    {
        $version = trim((string) config('social.facebook_graph_version', 'v24.0'));

        return "https://graph.facebook.com/{$version}/{$pageId}/feed";
    }

    /**
     * @param mixed $body
     */
    private function errorMessage(mixed $body, string $fallback): string
    {
        if (! is_array($body)) {
            return $fallback;
        }

        return (string) data_get($body, 'error.message', $fallback);
    }
}
