<?php

use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

it('can retrieve logs from sites', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->once()->with('personal', 2)->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'user' => 'forge', 'appType' => 'php']),
    );

    $files = [
        '/home/forge/something.com/shared/storage/logs/*.log',
        '/home/forge/something.com/storage/logs/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), [])
        ->andReturn([0, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('site:logs')
        ->expectsSearch(
            'Which site would you like to retrieve the logs from',
            answer: 2,
            search: 'something',
            answers: [2 => 'something.com'],
        )
        ->expectsPromptsInfo('Retrieving the latest site logs');
});

it('can tail logs from sites', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->once()->with('personal', 1)->andReturn(
        new Site(['id' => 1, 'name' => 'pestphp.com', 'user' => 'forge', 'appType' => 'wordpress']),
    );

    $files = [
        '/home/forge/pestphp.com/public/wp-content/*.log',
        '/home/forge/pestphp.com/wp-content/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), ['-f'])
        ->andReturn([0, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('site:logs', ['--follow' => true])
        ->expectsSearch(
            'Which site would you like to retrieve the logs from',
            answer: 1,
            search: 'pest',
            answers: [1 => 'pestphp.com'],
        )
        ->expectsPromptsInfo('Retrieving the latest site logs');
});

it('exits with 0 exit code on control + c', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->once()->with('personal', 1)->andReturn(
        new Site(['id' => 1, 'name' => 'pestphp.com', 'user' => 'forge', 'appType' => 'wordpress']),
    );

    $files = [
        '/home/forge/pestphp.com/public/wp-content/*.log',
        '/home/forge/pestphp.com/wp-content/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), ['-f'])
        ->andReturn([255, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('site:logs', ['--follow' => true])
        ->expectsSearch(
            'Which site would you like to retrieve the logs from',
            answer: 1,
            search: 'pest',
            answers: [1 => 'pestphp.com'],
        )
        ->expectsPromptsInfo('Retrieving the latest site logs');
});

it('displays errors', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->once()->with('personal', 2)->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'user' => 'user-in-isolation', 'appType' => 'php']),
    );

    $files = [
        '/home/user-in-isolation/something.com/shared/storage/logs/*.log',
        '/home/user-in-isolation/something.com/storage/logs/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), ['-f'])
        ->andReturn([1, [
            'ls: error',
        ]]);

    $this->artisan('site:logs', ['--follow' => true])
        ->expectsSearch(
            'Which site would you like to retrieve the logs from',
            answer: 2,
            search: 'something',
            answers: [2 => 'something.com'],
        )
        ->expectsPromptsInfo('Retrieving the latest site logs');
})->throws('The requested logs could not be found or they are empty.');
