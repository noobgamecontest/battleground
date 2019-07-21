<?php

namespace App\Providers;

use App\Services\TournamentService;
use Illuminate\Support\ServiceProvider;

class TournamentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TournamentService::class, function () {
            return new TournamentService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [TournamentService::class];
    }
}
