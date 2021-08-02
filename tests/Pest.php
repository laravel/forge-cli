<?php

use App\Clients\Forge;
use App\Commands\Command;
use App\Repositories\ConfigRepository;
use App\Repositories\ForgeRepository;
use App\Repositories\KeyRepository;
use App\Repositories\RemoteRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Testing\TestCase;
use Spatie\Once;
use Tests\CreatesApplication;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class, CreatesApplication::class)
    ->beforeEach(function () {
        Once\Cache::flush();

        (new Filesystem)->deleteDirectory(base_path('tests/.laravel-forge'));

        $this->client = tap(Mockery::mock(Forge::class), function ($mock) {
            $mock->shouldReceive('setApiKey')->zeroOrMoreTimes();
        });

        $this->config = resolve(ConfigRepository::class)->set('token', '123123213');

        $this->keys = resolve(KeyRepository::class);

        $this->forge = resolve(ForgeRepository::class)->setClient(
            $this->client
        );

        $this->remote = resolve(RemoteRepository::class);

        Command::resolveLatestVersionUsing(function () {
            return config('app.version');
        });
    })->afterEach(function () {
        (new Filesystem)->deleteDirectory(base_path('tests/.laravel-forge'));
    })->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

// ..
