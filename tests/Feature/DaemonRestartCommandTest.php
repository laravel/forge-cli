<?php

it('can restart daemons', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.0 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->with(1, 2)->once()->andReturn(
        (object) ['id' => 2, 'command' => 'php8.0 artisan queue:work', 'status' => 'installed'],
    );

    $this->client->shouldReceive('restartDaemon')->with(1, 2, false)->once();

    $this->artisan('daemon:restart')
        ->expectsChoice('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Restart</>', 'php8.0 artisan queue:work', [
            'php7.4 artisan websockets:serve', 'php8.0 artisan queue:work',
        ])->expectsOutput('==> Daemon Restart Initiated Successfully.');
});


it('can not restart daemons that are not running', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'restarting'],
        (object) ['id' => 2, 'command' => 'php8.0 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->with(1, 1)->once()->andReturn(
        (object) ['id' => 2, 'command' => 'php8.0 artisan queue:work', 'status' => 'restarting'],
    );

    $this->artisan('daemon:restart')
        ->expectsChoice('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Restart</>', 1, [
            'php7.4 artisan websockets:serve', 'php8.0 artisan queue:work',
        ]);
})->throws('This deamon is not installed or is not running.');
