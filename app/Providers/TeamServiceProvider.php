<?php

namespace App\Providers;

use App\Services\TeamService;
use Illuminate\Support\ServiceProvider;

class TeamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TeamService::class, function () {
            return new TeamService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [TeamService::class];
    }
}
