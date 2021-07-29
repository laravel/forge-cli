<?php

it('can retrieve logs from sites', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->once()->with(1, 2)->andReturn(
        (object) ['id' => 1, 'name' => 'something.com', 'username' => 'forge', 'app' => 'php'],
    );

    $files = [
        '/home/forge/something.com/shared/storage/logs/*.log',
        '/home/forge/something.com/storage/logs/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), [])
        ->andReturn(0);

    $this->artisan('site:logs')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Retrieve The Logs From</>', 2);
});

it('can tail logs from sites', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->once()->with(1, 1)->andReturn(
        (object) ['id' => 1, 'name' => 'pestphp.com', 'username' => 'forge', 'app' => 'wordpress'],
    );

    $files = [
        '/home/forge/pestphp.com/public/wp-content/*.log',
        '/home/forge/pestphp.com/wp-content/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), ['-f'])
        ->andReturn(0);

    $this->artisan('site:logs', ['--tail' => true])
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Retrieve The Logs From</>', 1);
});

it('exits with 0 exit code on control + c', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->once()->with(1, 1)->andReturn(
        (object) ['id' => 1, 'name' => 'pestphp.com', 'username' => 'forge', 'app' => 'wordpress'],
    );

    $files = [
        '/home/forge/pestphp.com/public/wp-content/*.log',
        '/home/forge/pestphp.com/wp-content/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), ['-f'])
        ->andReturn(255);

    $this->artisan('site:logs', ['--tail' => true])
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Retrieve The Logs From</>', 1);
});

it('displays errors', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->once()->with(1, 2)->andReturn(
        (object) ['id' => 1, 'name' => 'something.com', 'username' => 'user-in-isolation', 'app' => 'php'],
    );

    $files = [
        '/home/user-in-isolation/something.com/shared/storage/logs/*.log',
        '/home/user-in-isolation/something.com/storage/logs/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), ['-f'])
        ->andReturn(1);

    $this->artisan('site:logs', ['--tail' => true])
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Retrieve The Logs From</>', 2);
})->throws('The requested logs could not be found, or they are simply empty.');
