<?php

it('can retrieve a daemon status', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php artisan queue:work'],
    ]);

    $this->client->shouldReceive('daemon')->with(1, 1)->andReturn(
        (object) [
            'id' => 1,
            'command' => 'php artisan queue:work',
            'status' => 'installed',
            'user' => 'forge',
            'directory' => '/home/forge/example.com',
            'createdAt' => '2024-01-01 00:00:00',
        ],
    );

    $this->artisan('daemon:status')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Check The Status Of</>', 1)
        ->expectsOutput('==> Daemon Status: [php artisan queue:work]');
});

it('can retrieve a daemon status with an argument', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('daemon')->with(1, 1)->andReturn(
        (object) [
            'id' => 1,
            'command' => 'php artisan queue:work',
            'status' => 'installed',
            'user' => 'forge',
            'directory' => '/home/forge/example.com',
            'createdAt' => '2024-01-01 00:00:00',
        ],
    );

    $this->artisan('daemon:status', ['daemon' => 1])
        ->expectsOutput('==> Daemon Status: [php artisan queue:work]');
});
