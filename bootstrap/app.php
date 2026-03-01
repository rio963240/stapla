<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// アプリケーションの基本設定
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // リバースプロキシ（HTTPS終端）越しのリクエストで正しく URL を扱う（署名付きURL等に必須）
        $middleware->trustProxies(at: '*');
        // ミドルウェアのエイリアス登録
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            // ログイン済みユーザーのリダイレクト挙動をカスタマイズ
            'guest' => RedirectIfAuthenticated::class,
        ]);
        // LINE webhook はCSRF例外にする
        $middleware->validateCsrfTokens(except: [
            'line/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 例外処理のカスタマイズは未設定
        //
    })->create();
