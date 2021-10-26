<?php

namespace App\Providers;

use App\Clients\Forge;
use App\Repositories\ConfigRepository;
use App\Repositories\ForgeRepository;
use Illuminate\Support\ServiceProvider;

class ForgeServiceProvider extends ServiceProvider
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
        $this->app->singleton(ForgeRepository::class, function () {
            $config = resolve(ConfigRepository::class);
            $token = $config->get('token', $_SERVER['FORGE_API_TOKEN'] ?? getenv('FORGE_API_TOKEN') ?? null);

            $client = new Forge($token);

            return new ForgeRepository($config, $client);
        });
    }
}
