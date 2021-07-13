<?php

namespace App\Providers;

use App\Support\Shell;
use Illuminate\Support\ServiceProvider;
use Mockery;

class ShellServiceProvider extends ServiceProvider
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
        $this->app->singleton(Shell::class, function () {
            return isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'testing'
                ? Mockery::mock(Shell::class)
                : new Shell;
        });
    }
}
