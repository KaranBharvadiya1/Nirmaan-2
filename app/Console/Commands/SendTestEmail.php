<?php

namespace App\Console\Commands;

use App\Support\EmailSender;
use Illuminate\Console\Command;

class SendTestEmail extends Command
{
    protected $signature = 'email:test {recipient?}';
    protected $description = 'Send a branded test email using the stored templates';

    public function handle(): int
    {
        $recipient = $this->argument('recipient') ?? config('mail.from.address');

        $data = [
            'name' => config('branding.app_name', 'Nirmaan'),
            'verification_url' => config('app.url').'/',
        ];

        $sent = EmailSender::sendTemplate('welcome', $recipient, $data, $recipient);

        if (! $sent) {
            $this->error('Template not found or email could not be sent.');
            return 1;
        }

        $this->info("Test email queued to {$recipient}.");
        return 0;
    }
}
