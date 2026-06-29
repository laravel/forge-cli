<?php

use Illuminate\Support\Facades\File;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

it('can pull environment variables generated file', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production']),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com']),
    );

    $file = getcwd().'/.env.forge.2';

    File::shouldReceive('exists')->once()->with($file)->andReturn(false);
    File::shouldReceive('delete')->once()->with($file)->andReturn(false);

    $content = "BAR=FOO\nFOO=BAR\n";

    $this->client->shouldReceive('siteEnvironment')->once()->with('personal', 1, 2)->andReturn($content);

    File::shouldReceive('put')->once()->with($file, $content);

    $this->artisan('env:pull')
        ->expectsSearch(
            'Which site would you like to download the environment file from',
            answer: 2,
            search: 'something',
            answers: [2 => 'something.com'],
        )
        ->expectsPromptsInfo('Environment variables written to .env.forge.2');
});

it('can pull environment variables specific env file', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production']),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 1)->once()->andReturn(
        new Site(['id' => 1, 'name' => 'pestphp.com']),
    );

    File::shouldReceive('delete')->once()->with('.env')->andReturn(false);

    $content = "FOO=BAR\nBAR=FOO\n";

    $this->client->shouldReceive('siteEnvironment')->once()->with('personal', 1, 1)->andReturn($content);

    File::shouldReceive('put')->once()->with('.env', $content);

    $this->artisan('env:pull', ['site' => 'pestphp.com', 'file' => '.env'])
        ->expectsPromptsInfo('Environment variables written to .env');
});
