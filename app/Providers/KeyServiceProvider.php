<?php

namespace App\Providers;

use App\Repositories\KeyRepository;
use Illuminate\Support\ServiceProvider;
use Mockery;

class KeyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ..
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(KeyRepository::class, function () {
            return isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'testing'
                ? Mockery::mock(KeyRepository::class)
                : new KeyRepository($this->keysPath());
        });
    }

    /**
     * Returns the path that holds the keys.
     *
     * @return string
     */
    public function keysPath()
    {
        $path = ($_SERVER['HOME'] ?? $_SERVER['USERPROFILE']);

        return $path.'/.ssh';
    }
}
