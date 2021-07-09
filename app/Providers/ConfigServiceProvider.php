<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ConfigRepository;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ConfigRepository::class, function () {
            $path = $this->app->runningUnitTests()
                 ? ($_SERVER['HOME'] ?? $_SERVER['USERPROFILE'])
                 : base_path('tests');

            $path .= '/.laravel-forge/config.json';

            return new ConfigRepository($path);
        });
    }
}
