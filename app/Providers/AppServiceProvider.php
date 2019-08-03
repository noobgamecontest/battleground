<?php

namespace App\Providers;

use App\Models\Tournament;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('teamName', function ($attribute, $value, $parameters, $validator) {
            $datas = $validator->getData();
            $tournament = Tournament::find($datas['tournamentId']);

            $target = $tournament->teams->where('name', $value);

            return $target->isEmpty();
        });
    }
}
