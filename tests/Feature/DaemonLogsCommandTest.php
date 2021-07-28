<?php

it('can retrieve logs from daemons', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.0 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->andReturn(
        (object) ['id' => 1, 'user' => 'forge'],
    );

    $this->remote->shouldReceive('exec')
        ->once()
        ->with('cat /home/forge/.forge/daemon-1.log')
        ->andReturn([0, "   [00:01] FOO\n[00:02] BAR\n   "]);

    $this->artisan('daemon:logs')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Retrieve The Logs From</>', 2)
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can not retrieve logs when there is no log files', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.0 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->andReturn(
        (object) ['id' => 1, 'user' => 'forge'],
    );

    $this->remote->shouldReceive('exec')
        ->once()
        ->with('cat /home/forge/.forge/daemon-1.log')
        ->andReturn([1, '']);

    $this->artisan('daemon:logs')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Retrieve The Logs From</>', 2);
})->throws('The requested logs could not be found, or they are simply empty.');

it('can not retrieve logs from daemons run by root', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.0 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->andReturn(
        (object) ['id' => 1, 'user' => 'root'],
    );

    $this->artisan('daemon:logs')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Retrieve The Logs From</>', 1);
})->throws('Requesting logs from daemons run by [root] is not supported.');
