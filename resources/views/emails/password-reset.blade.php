<x-mail::message>
【スタプラ】パスワード再設定のご案内

{{ $greeting }}

以下のボタンをクリックして、パスワードの再設定を行ってください

<x-mail::button :url="$actionUrl">
パスワードを再設定する
</x-mail::button>

このリンクの有効期限は{{ $expire }}分です

もしこのメールに覚えがない場合は、破棄してください
</x-mail::message>
