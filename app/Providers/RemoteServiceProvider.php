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
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'testing') {
            return;
        }

        $socketsPath = $this->getSocketsPath();

        if (! File::isDirectory($socketsPath)) {
            File::makeDirectory($socketsPath);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RemoteRepository::class, function () {
            $path = ($_SERVER['HOME'] ?? $_SERVER['USERPROFILE']);

            return isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'testing'
                ? tap(Mockery::mock(RemoteRepository::class), function ($mock) {
                    // @phpstan-ignore-next-line
                    $mock->shouldReceive('resolveServerUsing')->zeroOrMoreTimes();
                }) : new RemoteRepository($this->getSocketsPath());
        });
    }

    /**
     * Returns the path that holds the sockets in use.
     *
     * @return string
     */
    public function getSocketsPath()
    {
        $path = ($_SERVER['HOME'] ?? $_SERVER['USERPROFILE']);

        return $path.'/.laravel-forge/sockets';
    }
}
