<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (config('mail.mailers.smtp.verify_peer') === false) {
            $this->app->singleton(MailManager::class, function ($app) {
                return new class($app) extends MailManager {
                    protected function createSmtpTransport(array $config)
                    {
                        $factory = new EsmtpTransportFactory();

                        $scheme = $config['scheme'] ?? null;
                        if (!$scheme) {
                            $scheme = !empty($config['encryption']) && $config['encryption'] === 'tls'
                                ? (($config['port'] ?? 465) == 465 ? 'smtps' : 'smtp')
                                : 'smtp';
                        }

                        $transport = $factory->create(new Dsn(
                            $scheme,
                            $config['host'],
                            $config['username'] ?? null,
                            $config['password'] ?? null,
                            $config['port'] ?? null,
                        ));

                        $stream = $transport->getStream();
                        if ($stream instanceof SocketStream) {
                            $stream->setStreamOptions([
                                'ssl' => [
                                    'allow_self_signed' => true,
                                    'verify_peer' => false,
                                    'verify_peer_name' => false,
                                ],
                            ]);
                        }

                        return $transport;
                    }
                };
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
