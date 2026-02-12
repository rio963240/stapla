<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録完了</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <p>{{ $user->name }} 様</p>

    <p>この度は、{{ config('app.name') }}にご登録いただき誠にありがとうございます。</p>

    <p>会員登録が完了いたしましたのでお知らせいたします。</p>

    <p><strong>▼会員専用ページログイン</strong></p>
    <p><a href="{{ url(route('login')) }}" style="color: #2563eb; word-break: break-all;">{{ url(route('login')) }}</a></p>

    <p><strong>【ご注意】</strong></p>
    <p>本メールに身に覚えの無い場合は、本メールを破棄していただきますようお願いいたします。</p>
    <p>※このメールは自動返信によって送信しています。<br>
    ご返信をいただいてもお返事できかねますのでご了承ください。</p>
</body>
</html>
