<?php

namespace App\Providers;

use App\Support\Time;
use Illuminate\Support\ServiceProvider;

class TimeServiceProvider extends ServiceProvider
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
        $this->app->singleton(Time::class, function () {
            return isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'testing'
                ? $this->fakeTime()
                : new Time();
        });
    }

    /**
     * Creates a new fake instance of Time.
     *
     * @return \App\Support\Time
     */
    public function fakeTime()
    {
        return new class extends Time
        {
            /**
             * Delays the code execution for the given number of seconds.
             *
             * @param  int  $seconds
             * @return void
             */
            public function sleep($seconds)
            {
                // ..
            }
        };
    }
}
