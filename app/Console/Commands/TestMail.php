<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'mail:test {to : Recipient email address}';

    protected $description = 'Send a test email to verify SMTP configuration';

    public function handle(): int
    {
        $to = $this->argument('to');

        $this->info("Sending test email to: {$to}");

        try {
            Mail::raw('This is a test email from Laravel. SMTP configuration is working!', function ($message) use ($to) {
                $message->to($to)
                    ->subject('SMTP Test - ' . config('app.name'));
            });

            $this->info('✓ Test email sent successfully!');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Failed to send email: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
