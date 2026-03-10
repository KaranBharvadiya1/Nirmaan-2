<?php

namespace App\Console;

use App\Console\Commands\SendTestEmail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        SendTestEmail::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        //
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }

    protected function bootstrap(): int
    {
        if (! $this->app->bound('request')) {
            $this->app->instance('request', \Illuminate\Http\Request::create(config('app.url')));
        }

        return parent::bootstrap();
    }
}
