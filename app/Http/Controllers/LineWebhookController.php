<?php

namespace App\Http\Controllers;

use App\Http\Middleware\LineWebhookRawBody;
use App\Models\LineAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LineWebhookController extends Controller
{
    /**
     * LINE Platform からの Webhook を受信する。
     * 署名検証のため、必ず生のリクエストボディを使用する。
     */
    public function __invoke(Request $request): Response
    {
        // 署名検証には必ず生ボディを使う（ミドルウェアで保持した生ボディを優先）
        $rawBody = $request->attributes->get(LineWebhookRawBody::KEY) ?? $request->getContent();
        $signature = $request->header('X-Line-Signature', '');

        if (!$this->verifySignature($rawBody, $signature)) {
            Log::warning('LINE webhook signature verification failed.', [
                'body_length' => strlen($rawBody),
                'signature_length' => strlen($signature),
                'secret_configured' => config('services.line.channel_secret') !== '',
            ]);

            return response('', 403);
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload) || !isset($payload['events'])) {
            return response('', 200);
        }

        foreach ($payload['events'] as $event) {
            $this->handleEvent($event);
        }

        return response('', 200);
    }

    private function verifySignature(string $body, string $signature): bool
    {
        $signature = trim($signature);
        if ($signature === '') {
            return false;
        }

        // .env のコピペで改行・スペースが入ることがあるため trim
        $channelSecret = trim(config('services.line.channel_secret', ''));
        if ($channelSecret === '') {
            return false;
        }

        $hash = base64_encode(hash_hmac('sha256', $body, $channelSecret, true));

        // LINE は複数署名をカンマ区切りで送る場合があるので、いずれか一致すれば OK
        $signatures = array_map('trim', explode(',', $signature));
        foreach ($signatures as $sig) {
            if (hash_equals($hash, $sig)) {
                return true;
            }
        }

        return false;
    }

    private function handleEvent(array $event): void
    {
        $type = $event['type'] ?? '';
        $lineUserId = $event['source']['userId'] ?? null;

        if ($lineUserId === null) {
            return;
        }

        switch ($type) {
            case 'follow':
                // 友だち追加: 既存の line_accounts で line_link_token のみ設定済みの行は
                // ここでは更新しない（メッセージでトークン送信時に紐づける）
                break;
            case 'unfollow':
                // ブロック/友だち解除: 紐づけを解除
                LineAccount::where('line_user_id', $lineUserId)->update([
                    'line_user_id' => null,
                    'line_link_token' => null,
                    'is_linked' => false,
                ]);
                break;
            case 'message':
                $message = $event['message'] ?? [];
                if (($message['type'] ?? '') === 'text') {
                    $this->handleTextMessage(trim((string) ($message['text'] ?? '')), $lineUserId);
                }
                break;
        }
    }

    /**
     * テキストメッセージ: 連携トークンと一致すれば line_user_id を紐づける
     */
    private function handleTextMessage(string $text, string $lineUserId): void
    {
        if ($text === '') {
            return;
        }

        $account = LineAccount::where('line_link_token', $text)
            ->whereNull('line_user_id')
            ->first();

        if (!$account) {
            return;
        }

        // 他ユーザーが既にこの line_user_id を使っていないか確認
        $exists = LineAccount::where('line_user_id', $lineUserId)->whereKeyNot($account->line_accounts_id)->exists();
        if ($exists) {
            return;
        }

        $account->update([
            'line_user_id' => $lineUserId,
            'line_link_token' => null,
            'is_linked' => true,
        ]);
    }
}
