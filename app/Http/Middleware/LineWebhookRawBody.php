<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * LINE Webhook 用: 署名検証で使うためリクエストボディを一度だけ読み、保持する。
 * プロキシや他のミドルウェアで body が読まれた場合に備える。
 */
class LineWebhookRawBody
{
    public const KEY = 'line_webhook_raw_body';

    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set(self::KEY, $request->getContent());

        return $next($request);
    }
}
