<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認証済みユーザー情報を返す（Sanctum）
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
