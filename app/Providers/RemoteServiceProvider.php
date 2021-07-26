<?php

namespace App\Providers;

use App\Repositories\RemoteRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Mockery;

class RemoteServiceProvider extends ServiceProvider
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
        $this->app->singleton(RemoteRepository::class, function () {
            return isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'testing'
                ? tap(Mockery::mock(RemoteRepository::class), function ($mock) {
                    // @phpstan-ignore-next-line
                    $mock->shouldReceive('resolveServerUsing')->zeroOrMoreTimes();
                }) : new RemoteRepository($this->ensureSocketsPath());
        });
    }

    /**
     * Returns the path that holds the sockets in use.
     *
     * @return string
     */
    public function ensureSocketsPath()
    {
        $path = ($_SERVER['HOME'] ?? $_SERVER['USERPROFILE']);

        $config = "$path/laravel-forge";

        if (! File::isDirectory($config)) {
            File::makeDirectory($config);
        }

        $socketsPath = "$config/sockets";

        if (! File::isDirectory($socketsPath)) {
            File::makeDirectory($socketsPath);
        }

        return $socketsPath;
    }
}
