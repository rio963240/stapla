<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class RegistrationCompleteMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * メール内ワンクリックログイン用の署名付きURL（有効24時間）。
     */
    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user
    ) {
        $this->loginUrl = URL::temporarySignedRoute(
            'login-via-email',
            now()->addHours(24),
            ['user' => $user->id]
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = config('app.name');

        return new Envelope(
            subject: "【{$appName}】会員登録が完了いたしました。",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-complete',
        );
    }
}
