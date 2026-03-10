<?php

namespace App\Support;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailSender
{
    /**
     * Send an email using a predefined template stored in the database.
     *
     * @param  array<string, mixed>  $data
     */
    public static function sendTemplate(string $templateName, string $recipientEmail, array $data = [], ?string $recipientName = null): bool
    {
        $template = EmailTemplate::where('name', $templateName)->first();

        if (! $template) {
            return false;
        }

        $subject = $template->render($template->subject, $data);
        $body = $template->render($template->body, $data);

        return static::send($recipientEmail, $subject, $body, $recipientName);
    }

    /**
     * Send a raw HTML email and share the branding assets.
     */
    public static function send(string $recipientEmail, string $subject, string $htmlBody, ?string $recipientName = null): bool
    {
        $branding = config('branding', []);
        $body = Str::of($htmlBody)->trim()->replace("\r\n", "\n")->toString();
        $logoUrl = $branding['logo'] ?? asset('images/logo-mark.svg');
        $socialLinks = $branding['social_links'] ?? [];

        Mail::send('emails.layout', compact('subject', 'body', 'logoUrl', 'socialLinks'), function ($message) use ($recipientEmail, $recipientName, $subject): void {
            $message->to($recipientEmail, $recipientName ?? null)->subject($subject);
        });

        return true;
    }
}
