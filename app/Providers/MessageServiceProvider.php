<?php

namespace App\Providers;

use App\Services\Message\MessageService;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MessageService::class, function ($app) {
            return new MessageService($app['session']);
        });
    }
}
