<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * 画面上部のナビゲーション（HOME のみのハンバーガー）を表示するか。
     * false のときは表示せず、サイドバーレイアウトの下側ヘッダーだけになる。
     */
    public function __construct(
        public bool $showNavigation = true
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
