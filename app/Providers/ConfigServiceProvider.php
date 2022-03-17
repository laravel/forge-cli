<?php

namespace App\Providers;

use App\Repositories\ConfigRepository;
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
            if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'testing') {
                $path = base_path('tests');
            } elseif (is_dir(getcwd() . '/.laravel-forge')) {
                $path = getcwd();
            } else {
                $path = ($_SERVER['HOME'] ?? $_SERVER['USERPROFILE']);
            }

            $path .= '/.laravel-forge/config.json';

            return new ConfigRepository($path);
        });
    }
}
