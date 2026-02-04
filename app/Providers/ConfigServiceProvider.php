<?php

namespace App\Providers;

use App\Repositories\ConfigRepository;
use App\Repositories\LocalConfigRepository;
use Illuminate\Support\ServiceProvider;

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
        $this->app->singleton(ConfigRepository::class, function () {
            $path = isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'testing'
                 ? base_path('tests')
                 : ($_SERVER['HOME'] ?? $_SERVER['USERPROFILE']);

            $path .= '/.laravel-forge/config.json';

            return new ConfigRepository($path);
        });

        $this->app->singleton(LocalConfigRepository::class, function () {
            return new LocalConfigRepository();
        });
    }
}
