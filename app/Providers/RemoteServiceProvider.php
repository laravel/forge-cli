<?php

namespace App\Providers;

use App\Repositories\RemoteRepository;
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
        //
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
                }) : new RemoteRepository;
        });
    }
}
