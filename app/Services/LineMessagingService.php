<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineMessagingService
{
    private string $channelAccessToken;

    private string $endpoint = 'https://api.line.me/v2/bot/message/push';

    public function __construct()
    {
        $this->channelAccessToken = config('services.line.channel_access_token', '');
    }

    /**
     * プッシュメッセージ（テキスト）を1件送信
     */
    public function pushText(string $lineUserId, string $text): bool
    {
        if ($this->channelAccessToken === '') {
            Log::warning('LINE channel access token is not set.');

            return false;
        }

        /** @var Response $response */
        $response = Http::withToken($this->channelAccessToken)
            ->post($this->endpoint, [
                'to' => $lineUserId,
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => $text,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::warning('LINE push failed.', [
                'line_user_id' => $lineUserId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }
}
